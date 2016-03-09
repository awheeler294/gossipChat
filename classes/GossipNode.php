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

    /**
     * @return array GossipNode
     */
    public static function getNodes() {
        $nodeInfoQuery = 'SELECT id, node_url
                            FROM gossip_nodes
                            WHERE  1';
        $stmt = Database::getDB()->prepare($nodeInfoQuery);
        $stmt->execute();
        $nodeInfoResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $nodes = array();
        foreach($nodeInfoResults as $nodeInfo) {
            $nodes[] = new GossipNode($nodeInfo['node_url']);
        }

        return $nodes;
    }

    /**
     * @return array GossipNode
     */
    public static function getChatNodes() {
        $nodeInfoQuery = 'SELECT id, node_url
                            FROM chat_nodes
                            WHERE  1';
        $stmt = Database::getDB()->prepare($nodeInfoQuery);
        $stmt->execute();
        $nodeInfoResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $nodes = array();
        foreach($nodeInfoResults as $nodeInfo) {
            $nodes[] = new GossipNode($nodeInfo['node_url']);
        }

        return $nodes;
    }

    public static function getLocalNode() {
        return LocalNode::build($_SERVER['HTTP_HOST'] . '/gossip/html');
    }

    /**
     * @return GossipNode
     */
    public static function getRandomNode() {
        $nodes = GossipNode::getNodes();

        $randomIndex = rand(0, count($nodes) - 1);

        $randomNode = $nodes[$randomIndex];

//        error_log(PHP_EOL . PHP_EOL);
//        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$nodes ' . print_r($nodes, true));
//        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$randomIndex ' . print_r($randomIndex, true));
//        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$randomNode ' . print_r($randomNode, true));
//        error_log(PHP_EOL . PHP_EOL);

        return $randomNode;
    }

    /**
     * @return GossipNode
     */
    public static function getRandomOtherNode() {
        $localNode = self::getLocalNode();

        $randomNode = self::getRandomNode();

        $fails = 0;
        while ($randomNode->getNodeURL() == $localNode->getNodeURL() && $fails < 10) {
            $randomNode = self::getRandomNode();
            $fails++;
        }

//        error_log(PHP_EOL . PHP_EOL);
//        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$randomNode ' . print_r($randomNode, true));
//        error_log(PHP_EOL . PHP_EOL);

        return $randomNode;
    }

    /**
     * @return GossipNode
     */
    public static function getRandomChatNode() {
        $nodes = GossipNode::getChatNodes();

        $randomIndex = rand(0, count($nodes) - 1);

        $randomNode = $nodes[$randomIndex];

        return $randomNode;
    }

    public static function processMessage(array $message) {
//        error_log(PHP_EOL . PHP_EOL);
//        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$message ' . print_r($message, true));
//        error_log(PHP_EOL . PHP_EOL);

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

        $randomNode = GossipNode::getRandomOtherNode();
        $nodeURL = $randomNode->getNodeURL();

//        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$nodeURL ' . print_r($nodeURL, true));

        self::sendRequest($nodeURL, $wantMessage);
    }

    private static function sendRumorMessage(ChatMessage $message, $url) {
        $localNode = self::getLocalNode();

        $rumorMessage = array('Rumor' => $message->toArray(), 'EndPoint' => $localNode->getNodeURL());
        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$url ' . print_r($url, true));
        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$rumorMessage ' . print_r($rumorMessage, true));

        self::sendRequest($url, $rumorMessage);

    }

    private static function sendRequest($url, $data) {
//        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$url ' . print_r($url, true));
        $parsedURL = parse_url($url);
//        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$parsedURL ' . print_r($parsedURL, true));
        if (!isset($parsedURL['scheme']) || !$parsedURL['scheme']) {
            $url = 'http://';
        }
        $url .= self::unparseUrl($parsedURL);

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
            error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$url ' . print_r($url, true));
            error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::data ' . print_r($data, true));
            error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$result ' . print_r($result, true));
        }

        return $result;
    }

    private static function unparseUrl($parsed_url) {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    public static function saveNode($nodeURL) {
        $insertNodeQuery = 'INSERT INTO gossip_nodes(node_url)
                            VALUES(:nodeURL)
                            ON DUPLICATE KEY UPDATE node_url = node_url';
        $stmt = Database::getDB()->prepare($insertNodeQuery);
        $stmt->bindValue('nodeURL', $nodeURL);
        $stmt->execute();

        return new GossipNode($nodeURL);
    }


    private static function processWantMessage($message) {
        error_log('[Gossip][' . __CLASS__ . '][' . __FUNCTION__ . ']::$message ' . print_r($message, true));

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
                self::sendRumorMessage($myMessage, $message['EndPoint']);
            }

        }
    }

    private static function processRumorMessage($message) {
        ChatMessage::saveRumorMessage($message['Rumor']['MessageID'], $message['Rumor']['Originator'], $message['Rumor']['Text']);
    }

}