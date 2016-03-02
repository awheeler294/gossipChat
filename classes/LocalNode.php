<?php

/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 3/2/2016
 * Time: 9:02 AM
 */
class LocalNode extends GossipNode {
    private $nodeURL;
    private $nodeId;

    public function getNodeURL() {
        return $this->nodeURL;
    }

    public function getNodeId() {
        return $this->nodeId;
    }


    public function __construct($nodeURL, $nodeId) {
        $this->nodeURL = $nodeURL;
        $this->nodeId = $nodeId;
    }

    public static function build($nodeURL) {

        $nodeInfoQuery = 'SELECT node_url, node_uuid
                            FROM local_node
                            WHERE node_url = :nodeURL';
        $stmt = Database::getDB()->prepare($nodeInfoQuery);
        $stmt->bindValue('nodeURL', $nodeURL);
        $stmt->execute();
        $nodeInfoResults = $stmt->fetch(PDO::FETCH_ASSOC);
//        error_log('[gossip][LocalNode][buildFromURL]::$nodeInfoResults ' . print_r($nodeInfoResults, true));

        if ($nodeInfoResults) {
            if (!$nodeInfoResults['node_uuid']) {
                $nodeId = self::createID();
                $localNode = self::addIdToNode($nodeURL, $nodeId);
            }
            else {
                $localNode = new LocalNode($nodeURL, $nodeInfoResults['node_uuid']);
            }
        }
        else {
            $localNode = self::createNode($nodeURL, self::createID());
        }

        return $localNode;

    }

    public static function getLocalNode() {
        return LocalNode::build($_SERVER['HTTP_HOST'] . '/gossip/html');
    }

    public static function createID() {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charId = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charId, 0, 8).$hyphen
            .substr($charId, 8, 4).$hyphen
            .substr($charId,12, 4).$hyphen
            .substr($charId,16, 4).$hyphen
            .substr($charId,20,12);
        return $uuid;
    }

    private static function createNode($nodeURL, $nodeId) {
        $insertNodeQuery = 'INSERT INTO local_node(node_url, node_uuid)
                            VALUES(:nodeURL, :nodeId)';
        $stmt = Database::getDB()->prepare($insertNodeQuery);
        $stmt->bindValue('nodeURL', $nodeURL);
        $stmt->bindValue('nodeId', $nodeId);
        $stmt->execute();

        return new LocalNode($nodeURL, $nodeId);
    }

    private static function addIdToNode($nodeURL, $nodeId) {
        $insertNodeQuery = 'UPDATE local_node
                            SET node_uuid = :nodeId
                            WHERE node_url = :nodeURL';
        $stmt = Database::getDB()->prepare($insertNodeQuery);
        $stmt->bindValue('nodeURL', $nodeURL);
        $stmt->bindValue('nodeId', $nodeId);
        $stmt->execute();

        return new LocalNode($nodeURL, $nodeId);
    }

}