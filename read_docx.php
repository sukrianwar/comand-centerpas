<?php
$zip = new ZipArchive;
if ($zip->open('docs/PRD_Guard_Tour_v1.4.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    $text = strip_tags(str_replace('</w:p>', "\n", $xml));
    echo $text;
} else {
    echo 'failed';
}
