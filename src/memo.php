<?php

$title = '';
$content = '';

//登録用関数
function createMemo($link){
    $input = [];
    //入力
    echo 'タイトル：';
    $input['title'] = trim(fgets(STDIN));
    echo '内容：';
    $input['content'] = trim(fgets(STDIN));

    //バリデーション処理
    $validated = validate($input);
    if(!empty($validated)){
        foreach($validated as $error){
            echo $error . PHP_EOL;
        }
        return;
    }

    $sql =<<<SQL
    INSERT INTO memo(title, content) VALUES(
        "{$input['title']}",
        "{$input['content']}"
    );
    SQL;

    mysqli_query($link, $sql);

}

//バリデーション処理用関数
function validate($input){
    $errors = [];

    //タイトルのバリデーション処理
    if(!mb_strlen($input['title'])){
        $errors['title'] = 'タイトルを入力してください';
    } elseif(mb_strlen($input['title']) > 50){
        $errors['title'] = 'タイトルは50文字以内で入力してください';
    }

    //内容のバリデーション処理
    if(!mb_strlen($input['content'])){
        $errors['content'] = '内容を入力してください';
    } elseif(mb_strlen($input['content']) > 255){
        $errors['content'] = '内容は255文字以内で入力してください';
    }

    return $errors;
}

//表示用関数
function listMemo($link){
    $sql = 'SELECT title, content FROM memo';
    $memos = mysqli_query($link, $sql);
    $i=1;
    while($memo = mysqli_fetch_assoc($memos)){
        echo '～～～メモ' .$i .'～～～'.PHP_EOL;
        echo 'タイトル：' . $memo["title"].PHP_EOL;
        echo '内容：' . $memo["content"].PHP_EOL;
        $i++;
    }
}

$link = mysqli_connect('db','book_log','pass','book_log');

while(true){
echo '===============メモ帳==============='.PHP_EOL;
echo 'メニューを選択してください'.PHP_EOL;
echo '1：登録'.PHP_EOL;
echo '2：表示'.PHP_EOL;
echo '9：終了'.PHP_EOL;
echo '番号を入力してください(1,2,9)：';

    $selectedMenu = trim(fgets(STDIN));

    if($selectedMenu === '1'){
        //登録処理
        createMemo($link);
    } elseif($selectedMenu === '2'){
        //表示処理
        listMemo($link);
    } elseif($selectedMenu === '9'){
        mysqli_close($link);
        break;
    }

}
