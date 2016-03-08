<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/gossip/config/settings.php';


header("Access-Control-Allow-Orgin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");


//GossipNode::processMessage(array(
//        'Rumor' => array(
//            'MessageID'  => 'ABCD-1234-ABCD-1234-ABCD-1234:5' ,
//            'Originator' => 'Phil',
//            'Text'       => 'Hello World!',
//        ),
//        'EndPoint' => 'https://example.com/gossip/13244',
//    )
//);
//GossipNode::processMessage(array(
//        'Rumor' => array(
//            'MessageID'  => '314BFC97-F451-186C-75CD-E777D2BCC043:5' ,
//            'Originator' => 'Andrew',
//            'Text'       => 'test js',
//        ),
//        'EndPoint' => 'https://example.com/gossip/13244',
//    )
//);

//error_log('[Gossip][index.php]::$_REQUEST ' . print_r($_REQUEST, true));
//error_log(PHP_EOL . PHP_EOL);
//error_log('[Gossip][index.php]::file_get_contents("php://input") ' . print_r(file_get_contents("php://input"), true));
//error_log(PHP_EOL . PHP_EOL);
//error_log('[Gossip][index.php] Headers: ' . print_r(getallheaders(), true));


$data = file_get_contents("php://input");

if ($data) {
    GossipNode::processMessage(json_decode($data, true));
}
else {
    header("Location: /gossip/html/home.php");
}