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

    public static function processMessage(array $message) {
        if (isset($message['Want'])) {
            self::processWantMessage($message);
        }
        else if (isset($message['Rumor'])) {
            self::processRumorMessage($message);
        }
    }

    public static function sendWantMessage(array $messageIDs) {
        $localNode = self::getLocalNode();

        $wantMessage = array('Want' => $messageIDs, 'EndPoint' => $localNode->getNodeURL() . '/gossip_ear.php');

        $randomNode = GossipNode::getRandomNode();
        $nodeURL = $randomNode->getNodeURL();
        $nodeURL = "http://$nodeURL/";

//        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$nodeURL ' . print_r($nodeURL, true));

        self::sendRequest($nodeURL, $wantMessage);
    }

    private static function sendRumorMessage(ChatMessage $message, $url) {
        $localNode = self::getLocalNode();

        $rumorMessage = array('Rumor' => $message->toArray(), 'EndPoint' => $localNode->getNodeURL());

        self::sendRequest($url, $rumorMessage);

    }

    private static function sendRequest($url, $data) {

        $data = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $result = curl_exec($ch);

//        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$result ' . print_r($result, true));

        //curl errors
        if(curl_errno($ch) != 0)
        {
            error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . '] - invalid curl error', E_USER_WARNING);
        }

        return $result;
    }

    private static function saveNode($nodeURL) {
        $insertNodeQuery = 'INSERT INTO gossip_nodes(node_url)
                            VALUES(:nodeURL)';
        $stmt = Database::getDB()->prepare($insertNodeQuery);
        $stmt->bindValue('nodeURL', $nodeURL);
        $stmt->execute();

        return new GossipNode($nodeURL);
    }


    private static function processWantMessage($message) {

        $myMessages = ChatMessage::getMessages();

        foreach ($myMessages as $myMessage) {
            $found = false;

            foreach ($message['Want'] as $messageID) {
                if ($myMessage->getMessageID() == $messageID) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                self::sendRumorMessage($myMessage);
            }

        }
    }

    private static function processRumorMessage($message) {
        ChatMessage::saveRumorMessage($message['Rumor']['MessageID'], $message['Rumor']['Originator'], $message['Rumor']['Text']);
    }

}