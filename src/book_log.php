<?php

//mysql接続用関数
function dbConnect(){
    $link = mysqli_connect('db', 'book_log', 'pass', 'book_log');

    if(!$link){
        echo 'データベースに接続できません'. PHP_EOL;
        echo 'Debugging error: '. mysqli_connect_error() .PHP_EOL;
        exit;
    }

    return $link;
};

//バリデーション処理用関数
function validate($review){
    $errors = [];

    //書籍名が正しく入力されているかチェック
    if(!mb_strlen($review['title'])){
        $errors['title'] = '書籍名を入力してください';
    } elseif(mb_strlen($review['title']) > 255){
        $errors['title'] = '書籍名は255文字以内にしてください';
    }

    //著者名が正しく入力されているかチェック
    if(!mb_strlen($review['author'])){
        $errors['author'] = '著者名を正しく入力してください';
    } elseif(mb_strlen($review['author']) > 255){
        $errors['author'] = '書籍名は255文字以内にしてください';
    }

    //読書状況が正しく入力されているかチェック
    if(!mb_strlen($review['progress'])){
        $errors['progress'] = '読書状況を入力してください';
    } elseif( !in_array($review['progress'], ['未読','読んでる', '読了'], true)){
        $errors['progress'] = '「未読」「読んでる」「読了」のいずれかを入力してください';
    }

    //評価が１以上５未満で入力されているかをチェック
    if($review['evaluation'] < 1 || $review['evaluation'] > 5){
        $errors['evaluation'] = '評価は１～５の整数で入力してください';
    }

    //感想が正しく入力されているかチェック
    if(!mb_strlen($review['comment'])){
        $errors['comment'] = '感想を正しく入力してください';
    } elseif(mb_strlen($review['comment']) > 255){
        $errors['comment'] = '感想は255文字以内にしてください';
    }

    return $errors;
}
//読書ログ登録用関数
function createReview($link){
    $review = [];

    echo '読書ログを登録してください'.PHP_EOL;

    echo '書籍名：';
    $review['title'] = trim(fgets(STDIN));

    echo '著者名：';
    $review['author'] =  trim(fgets(STDIN));

    echo '読書状況：';
    $review['progress'] =  trim(fgets(STDIN));

    echo '評価：';
    $review['evaluation'] = (int) trim(fgets(STDIN));

    echo '感想：';
    $review['comment'] =  trim(fgets(STDIN));

    //ヴァリデーション処理
    $validated = validate($review);
    if(count($validated) > 0){
        foreach($validated as $error){
            echo $error .PHP_EOL;
        }
        return;
    }

    $sql =<<<EOT
    INSERT INTO reviews(
        title,
        author,
        progress,
        evaluation,
        comment
    ) VALUES (
        "{$review['title']}",
        "{$review['author']}",
        "{$review['progress']}",
        "{$review['evaluation']}",
        "{$review['comment']}"
    )
    EOT;

    //SQL実行
    $result = mysqli_query($link,$sql);

    if($result){
        //異常系処理
        echo '登録が完了しました。'.PHP_EOL.PHP_EOL;
    } else {
        //正常系処理
        echo 'データを登録できませんでした'.PHP_EOL;
        echo 'Debugging error : ' .mysqli_error($link).PHP_EOL;
    }

}

//読書ログ表示用関数
function showReviews($link){
    echo '読書ログを表示します'.PHP_EOL.PHP_EOL;

    $sql = 'SELECT * FROM reviews';

    //SQL実行
    $reviews = mysqli_query($link, $sql);

    $i = 1;

    while($review = mysqli_fetch_assoc($reviews)){
        echo '-----No'.$i.'-----'.PHP_EOL;
        echo '書籍名：' .$review['title'] .PHP_EOL;
        echo '著者名：'.$review['author'] .PHP_EOL;
        echo '読書状況：'.$review['progress'] .PHP_EOL;
        echo '評価：'.$review['evaluation'] .PHP_EOL;
        echo '感想：'.$review['comment'] .PHP_EOL;
        echo '--------------'.PHP_EOL;
        $i++;
    }

    mysqli_free_result($reviews);
}

$title = '';
$author = '';
$progress = '';
$evaluation = '';
$comment = '';

$reviews = [];

$link = dbConnect();

while(true){

    echo '1. 読書ログを登録'.PHP_EOL;
    echo '2. 読書ログを表示'.PHP_EOL;
    echo '9. アプリケーションを終了'.PHP_EOL;
    echo '番号を選択してください(1,2,9)：';
    $num = trim(fgets(STDIN));


    if($num === '1'){
        //読書ログを登録する
        createReview($link);

    } elseif($num === '2'){
        //読書ログを表示する
        showReviews($link);

    } elseif($num === '9'){
        //mysql切断
        mysqli_close($link);
        //アプリケーションを終了する
        break;
    }

}
