<?php

/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 2/29/2016
 * Time: 8:42 AM
 */
class GossipNode {
    private $nodeURL;

    public function getNodeURL() {
        return $this->nodeURL;
    }

    public function __construct($nodeURL) {
        $this->nodeURL = $nodeURL;
    }

    public static function build($nodeURL) {

        $nodeInfoQuery = 'SELECT id, node_url
                            FROM gossip_nodes
                            WHERE node_url = :nodeURL';
        $stmt = Database::getDB()->prepare($nodeInfoQuery);
        $stmt->bindValue('nodeURL', $nodeURL);
        $stmt->execute();
        $nodeInfoResults = $stmt->fetch(PDO::FETCH_ASSOC);
//        error_log('[gossip][ChatNode][buildFromURL]::$nodeInfoResults ' . print_r($nodeInfoResults, true));

        if ($nodeInfoResults) {
            $gossipNode = new GossipNode($nodeURL);
        }
        else {
            $gossipNode = self::saveNode($nodeURL);
        }

        return $gossipNode;

    }

    public static function getNodes() {
        $nodeInfoQuery = 'SELECT id, node_url
                            FROM gossip_nodes
                            WHERE  1';
        $stmt = Database::getDB()->prepare($nodeInfoQuery);
        $stmt->execute();
        $nodeInfoResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $nodes = array();
        foreach($nodeInfoResults as $nodeInfo) {
            $nodes[] = self::build($nodeInfo['node_url']);
        }

        return $nodes;
    }

    public static function getLocalNode() {
        return LocalNode::build($_SERVER['HTTP_HOST'] . '/gossip/html');
    }

    public static function getRandomNode() {
        $nodes = GossipNode::getNodes();

        $randomNode = rand(0, count($nodes) - 1);

        $node = $nodes[$randomNode];

        return $node;
    }

    public static function sendWantMessage(array $messageIDs) {
        $localNode = self::getLocalNode();

        $wantMessage = array('Want' => $messageIDs, 'EndPoint' => $localNode->getNodeURL() . '/gossip_ear.php');

        $randomNode = GossipNode::getRandomNode();
        $nodeURL = $randomNode->getNodeURL();

        $wantMessage = json_encode($wantMessage);

        $ch = curl_init($nodeURL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $wantMessage);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($wantMessage))
        );

        $result = curl_exec($ch);

    }

    private static function saveNode($nodeURL) {
        $insertNodeQuery = 'INSERT INTO gossip_nodes(node_url)
                            VALUES(:nodeURL)';
        $stmt = Database::getDB()->prepare($insertNodeQuery);
        $stmt->bindValue('nodeURL', $nodeURL);
        $stmt->execute();

        return new GossipNode($nodeURL);
    }


}