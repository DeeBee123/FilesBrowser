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
    <header class="mdc-top-app-bar--outlined">
        <div class="mdc-top-app-bar__row">
            <section class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
                <button class="mdc-fab mdc-fab--extended" onclick="<?php echo ("history.go(-1)") ?>">
                    <span class="material-icons">
                        arrow_back
                    </span>
                </button>
                <span class="mdc-top-app-bar__title"> Current directory: 
                <?php echo ($_SERVER['REQUEST_URI']);?> </span>
            </section>
        </div>
    </header>
    <main class="mdc-top-app-bar--fixed-adjust">
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

                    <tbody class="mdc-data-table__content">
                        <?php
                            $root = getcwd();
                            $cur_final = $root  . $_GET["path"];
                            $scanned_directory = array_diff(scandir($cur_final), array('..', '.'));
                            $path = empty($_GET["path"]) ? $_SERVER['REQUEST_URI'] . "?path=/" : $_SERVER['REQUEST_URI'] . "/";
                        
                            if($_GET["delete"]){
                             unlink(".".$_GET["path"].htmlentities($_GET["delete"]));
                             header("Location: ".dirname($_GET["delete"]));
                            }
                            
                            foreach ($scanned_directory as $dir) {
                                $type = is_dir(".". $_GET["path"]. "/" . $dir) ? "Directory" : (is_file(".". $_GET["path"]. "/" . $dir) ? "File" : "undefined");
                                $deleteBtn = $dir === "index.php" || $dir === "README.md" ? " <button class=\"mdc-button\"  disabled>
                                <div class=\"mdc-button__ripple\"></div>
                                <span class=\"mdc-button__label\">Delete</span>
                                </button>" :($type === "File" ? " <a href=?delete=".$path.$dir . " class=\"mdc-button\"  >
                                    <div class=\"mdc-button__ripple\"></div>
                                    <span class=\"mdc-button__label\">Delete</span>
                                    </a>"  : "");
                                $link = $type === "Directory" ? "<a href=\"{$path}{$dir}\" >" . $dir . "</a>" : $dir;
                                echo ("
                                <tr class=\"mdc-data-table__row\">
                                    <th class=\"mdc-data-table__cell\" scope=\"row\">" . $type . "</th>
                                    <td class=\"mdc-data-table__cell mdc-data-table__cell--numeric\">" . $link . "</td>
                                    <td class=\"mdc-data-table__cell\">"
                                        . $deleteBtn .
                                        "
                                    </td>
                                </tr>");
                            }
                        ?>
                    </tbody>
                </table>
            </div>
                <?php 
                isset($_POST["folder"]) ?(file_exists(".".$_GET["path"]."/" .htmlentities($_POST["folder"]))? null :mkdir( ".".$_GET["path"]."/" .htmlentities($_POST["folder"]))) : null ?>  
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
                <button class="mdc-fab mdc-fab--extended" onclick=<?php echo "history.go(-1)"; ?>>
                    <div class="mdc-fab__ripple"></div>
                    <span class="material-icons mdc-fab__icon">add</span>
                    <span class="mdc-fab__label">Upload file</span>
                </button>
        </div> 
    </main>
</body>