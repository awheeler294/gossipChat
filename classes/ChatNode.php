<?php

/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 2/29/2016
 * Time: 8:42 AM
 */
class ChatNode {
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

    public static function build($nodeURL, $nodeId = false) {

        $nodeInfoQuery = 'SELECT id, node_url, node_uuid
                            FROM gossip_nodes
                            WHERE node_url = :nodeURL';
        $stmt = Database::getDB()->prepare($nodeInfoQuery);
        $stmt->bindValue('nodeURL', $nodeURL);
        $stmt->execute();
        $nodeInfoResults = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log('[gossip][ChatNode][buildFromURL]::$nodeInfoResults ' . print_r($nodeInfoResults, true));

        if ($nodeInfoResults) {
            if (!$nodeInfoResults['node_uuid']) {
                if (!$nodeId) {
                    $nodeId = self::createID();
                }
                $chatNode = self::addIdToNode($nodeURL, $nodeId);
            }
            else {
                $chatNode = new ChatNode($nodeURL, $nodeInfoResults['node_uuid']);
            }
        }
        else {
            $chatNode = self::createNode($nodeURL, self::createID());
        }

        return $chatNode;

    }

    public static function getNodes() {
        $nodeInfoQuery = 'SELECT id, node_url, node_uuid
                            FROM gossip_nodes
                            WHERE  1';
        $stmt = Database::getDB()->prepare($nodeInfoQuery);
        $stmt->execute();
        $nodeInfoResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $nodes = array();
        foreach($nodeInfoResults as $nodeInfo) {
            $nodes[] = self::build($nodeInfo['node_url'], $nodeInfo['node_uuid']);
        }

        return $nodes;
    }

    public static function getLocalNode() {
        return self::build($_SERVER['HTTP_HOST']);
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
        $insertNodeQuery = 'INSERT INTO gossip_nodes(node_url, node_uuid)
                            VALUES(:nodeURL, :nodeId)';
        $stmt = Database::getDB()->prepare($insertNodeQuery);
        $stmt->bindValue('nodeURL', $nodeURL);
        $stmt->bindValue('nodeId', $nodeId);
        $stmt->execute();

        return new ChatNode($nodeURL, $nodeId);
    }

    private static function addIdToNode($nodeURL, $nodeId) {
        $insertNodeQuery = 'UPDATE gossip_nodes
                            SET node_uuid = :nodeId
                            WHERE node_url = :nodeURL';
        $stmt = Database::getDB()->prepare($insertNodeQuery);
        $stmt->bindValue('nodeURL', $nodeURL);
        $stmt->bindValue('nodeId', $nodeId);
        $stmt->execute();

        return new ChatNode($nodeURL, $nodeId);
    }
}