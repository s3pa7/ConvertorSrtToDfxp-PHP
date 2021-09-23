<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require 'helper.php';
require 'constant.php';
?>
<html>
<link rel="stylesheet" href="assets/css/style.css" media="screen">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script src="assets/js/index.js"></script>

</html>
<body>
<div class="container">
    <header>SRT to DFXP</header>
</div>
<form id="application-form" method="post" enctype="multipart/form-data">


    <div class="box">
        <div>
            <h3>Choose .srt file from your computer.</h3>
            <input type="file" name="file" id="fileToUpload" class="custom-file-input">
            <label for="fileToUpload">Upload</label>
        </div>
        <div class="text-p">
            <span id="text"></span>
        </div>
    </div>
    <div class="button">
        <button  type="submit" class="btn">
            <i class="fa fa-edit"></i>Convert
        </button>
    </div>
</form>
</body>

<?php


if(!empty($_FILES)){

    $allowed = array( 'srt' );
    if ($_FILES["file"]["error"] > 0) {

        echo '<div>
         <div class="hide" style="text-align: center">File is whrong</div>
        </div>
        <div>';
    }
    $ext = pathinfo( $_FILES['file']['name'], PATHINFO_EXTENSION );

    if( !in_array( $ext, $allowed ) ) {

        echo '<div>
         <div class="hide" style="text-align: center">This file support only .srt files</div>
        </div>
        <div>';

        //echo "This file support only .srt files<br />";
        die();
    }




    else {

        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $path = UPLOAD_DIR . '/SRT' . '/';
        $filename = $_FILES["file"]["name"];
        //$filename = md5( uniqid( '', true ) ).'.'.$ext;
        $basePath = $path . $filename;
        if( !is_dir( $path ) ) {
            $oldmask = umask( 0 );
            mkdir( $path, 0777, true );
            umask( $oldmask );
        }

        if( !move_uploaded_file( $_FILES['file']['tmp_name'], $basePath) ) {

           echo 'Failed to move uploaded file.';
           die();
        }
        $getContent = file_get_contents($basePath);


        $getExtension = fileExtension($getContent);

        $normalize = normalizeNewLines(removeUtf8Bom($getContent));

        // create array with content
        $fileContentToInternalFormat = fileContentToInternalFormat($normalize);

        $filePath = UPLOAD_DIR . '/DFXP/ShadowAndBoneS01E01.dfxp';

        $internalFormatToFileContent = internalFormatToFileContent($fileContentToInternalFormat);

        array_dfxp_download($filePath ,$internalFormatToFileContent);






    }


}
?>
