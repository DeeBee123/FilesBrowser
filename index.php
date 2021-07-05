<?php

session_start();

// logout logic
if (isset($_GET['action']) and $_GET['action'] == 'logout') {
    // session_start();
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['logged_in']);
    print('Logged out!');
}

// login logic
$msg = '';
if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    if ($_POST['username'] == 'User' && $_POST['password'] == '1234') {
        $_SESSION['logged_in'] = true;
        $_SESSION['timeout'] = time();
        $_SESSION['username'] = $_POST['username'];
        $msg = 'You have entered valid username and password';
    } else {
        $msg = 'Wrong username or password';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Files Browser</title>
    <link href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css" rel="stylesheet">
    <script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        .mdc-data-table {
            align-items: center;
            width: 80%;
            margin: auto;
            display: flex;
        }

        .mdc-button:not(:disabled) {
            color: var(--mdc-theme-secondary);

        }
    </style>
    <script>
        const input = document.querySelector(".mdc-text-field__input");
        function clearValue(){
        input= "";
        }
    </script>
</head>
<body class="mdc-typography">

   
    <?php
    if ($_SESSION['logged_in'] == false) {
        print(' <h2>Enter Username and Password</h2>' );
        
    }
    ?>
<div>
    <?php
    if ($_SESSION['logged_in'] == true) {
        print('<h1>Hello, '.$_SESSION['username']  .'</h1>' );
    
    }
    ?>
</div>
<div>
<form action="./index.php" method="post" <?php $_SESSION['logged_in'] == true ? print("style = \"display: none\"") : print("style = \"display: block\"") ?>>
        <h4><?php echo $msg; ?></h4>
      
        <label class="mdc-text-field mdc-text-field--filled">
            <span class="mdc-text-field__ripple">username = User</span>
            <input class="mdc-text-field__input" type="text" aria-labelledby="my-label-id" name="username" required autofocus>
            <span class="mdc-line-ripple"></span>
        </label>
        <label class="mdc-text-field mdc-text-field--filled">
            <span class="mdc-text-field__ripple">password = 1234</span>
            <input class="mdc-text-field__input" type="password" aria-labelledby="my-label-id" name="password" required>
            <span class="mdc-line-ripple"></span>
        </label>
        <button class="mdc-button" type="submit" name="login">Login</button>
    </form>
    <?php
    if ($_SESSION['logged_in'] == true) {
        print('Click here to <a href="index.php?action=logout"> logout.</a>' );
    
    }
    ?>
    
</div>
    <header class="mdc-top-app-bar--outlined" style=<?php $_SESSION['logged_in'] == false ? print("\"display: none\"") : print("Z\"display: block\"") ?>>
        <div class="mdc-top-app-bar__row">
            <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
                <button class="mdc-fab mdc-fab--extended" onclick="<?php echo ("history.go(-1)") ?>">
                    <span class="material-icons">
                        arrow_back
                    </span>
                </button>
                <span class="mdc-top-app-bar__title"> Current directory: 
                <?php echo ($_SERVER['REQUEST_URI']);?> 
                
                </span>
            </section>
        </div>
    </header>
    <?php print(($_GET["delete"])); ?>
    <?php 
    if(isset($_POST['download'])){
        // print('Path to download: ' . './' . $_GET["path"] . $_POST['download']);
        $file='./'  . $_POST['download'];
        // a&nbsp;b.txt --> a b.txt
        $fileToDownloadEscaped = str_replace("&nbsp;", " ", htmlentities($file, null, 'utf-8'));

        ob_clean();
        ob_start();
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf'); // mime type → ši forma turėtų veikti daugumai failų, su šiuo mime type. Jei neveiktų reiktų daryti sudėtingesnę logiką
        header('Content-Disposition: attachment; filename=' . basename($fileToDownloadEscaped));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fileToDownloadEscaped)); // kiek baitų browseriui laukti, jei 0 - failas neveiks nors bus sukurtas
        ob_end_flush();

        readfile($fileToDownloadEscaped);
        exit;
    }
    ?> 
    <main class="mdc-top-app-bar--fixed-adjust" <?php $_SESSION['logged_in'] == false ? print("style = \"display: none\"") : print("style = \"display: block\"") ?>>
        <div class="mdc-data-table">
            <div class="mdc-data-table__table-container mdc-layout-grid">
                <table class="mdc-data-table__table" aria-label="Files browser">
                    <thead>
                        <tr class="mdc-data-table__header-row">
                            <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Type</th>
                            <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Name</th>
                            <th class="mdc-data-table__header-cell" role="columnheader" scope="col">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="mdc-data-table__content" >
                        <?php
                    
                            $root = getcwd();
                            $cur_final = $root  . $_GET["path"];
                            $scanned_directory = array_diff(scandir($cur_final), array('..', '.'));
                            $path = empty($_GET["path"]) ? $_SERVER['REQUEST_URI'] . "?path=/" : $_SERVER['REQUEST_URI'] . "/";
                        
                            if (isset($_POST["delete"])){
                             echo($_GET["delete"]);
                             unlink('./'  . $_POST['delete']);
                            //  header("Location: ". "/");
                            }
                            
                            foreach ($scanned_directory as $dir) {
                                $type = is_dir(".". $_GET["path"]. "/" . $dir) ? "Directory" : (is_file(".". $_GET["path"]. "/" . $dir) ? "File" : "undefined");
                                $deleteBtn = $dir === "index.php" || $dir === "README.md" ? " <button class=\"mdc-button\"  disabled>
                                <div class=\"mdc-button__ripple\"></div>
                                <span class=\"mdc-button__label\">Delete</span>
                                </button>" :($type === "File" ? " <form action=".$path.$dir. " class=\"mdc-button\"  method=\"post\">
                                
                                <button class=\"mdc-button\" type=\"submit\" name=\"delete\" value=\"" . $dir . "\">Delete</>
                                </form>"  : "");
                                    $downloadBtn = ($type === "File" ? " <form action=".$path.$dir. " class=\"mdc-button\"  method=\"post\">
                                
                                    <button class=\"mdc-button\" type=\"submit\" name=\"download\" value=\"" . $dir . "\">Download</>
                                    </form>"  : "");
                                $link = $type === "Directory" ? "<a href=\"{$path}{$dir}\" >" . $dir . "</a>" : $dir;
                                echo ("
                                <tr class=\"mdc-data-table__row\">
                                    <th class=\"mdc-data-table__cell\" scope=\"row\">" . $type . "</th>
                                    <td class=\"mdc-data-table__cell mdc-data-table__cell--numeric\">" . $link . "</td>
                                    <td class=\"mdc-data-table__cell\">"
                                        . $deleteBtn . $downloadBtn .
                                        "
                                    </td>
                                </tr>");
                            }
                        ?>
                    </tbody>
                </table>
            </div>
                <?php 
                function createFolder(){
                    mkdir( ".".$_GET["path"]."/" .htmlentities($_POST["folder"]));
                  
                    
                }
                isset($_POST["folder"]) ?(file_exists(".".$_GET["path"]."/" .htmlentities($_POST["folder"]))? null :createFolder()) : null ?>  
                <form  method="POST" style="margin: 50px 0;">
                    <label class="mdc-text-field mdc-text-field--filled">
                        <span class="mdc-text-field__ripple"></span>
                        <input class="mdc-text-field__input" type="text" aria-labelledby="my-label-id" name="folder" >
                        <span class="mdc-line-ripple"></span>
                    </label>
                    <button class="mdc-button mdc-button--outlined" type="submit" onclick="() =>clearValue()">
                        <span class="mdc-button__ripple"></span>
                        <span class="mdc-button__label">Create Folder</span>
                    </button>
                </form>
                <?php 
                if(isset($_FILES['upload'])){
                    $errors= array();
                    $file_name = $_FILES['upload']['name'];
                    $file_size = $_FILES['upload']['size'];
                    $file_tmp = $_FILES['upload']['tmp_name'];
                    $file_type = $_FILES['upload']['type'];
                    // check extension (and only permit jpegs, jpgs and pngs)
                    $file_ext = strtolower(end(explode('.',$_FILES['upload']['name'])));
                    $extensions = array("jpeg","jpg","png");
                    if(in_array($file_ext,$extensions)=== false){
                        $errors[]="extension not allowed, please choose a JPEG or PNG file.";
                    }
                    if($file_size > 2097152) {
                        $errors[]='File size must be smaller than 2 MB';
                    }
                    if(empty($errors)==true) {
                        move_uploaded_file($file_tmp,".".$_GET["path"]."/" .$file_name);
                    
                    }else{
                        print_r($errors);
                    }
                }

                ?>
                <form action="" method="post" enctype="multipart/form-data">
                <input type="file" name="upload" >
                <button class="mdc-button" type="submit">Upload </>
                </input>
               
                </form>
        </div> 
    </main>
</body>

