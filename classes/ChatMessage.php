<?php

/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 3/1/2016
 * Time: 9:10 PM
 */
class ChatMessage
{
    protected $MessageID;
    protected $Originator;
    protected $Text;

    public function __construct($MessageID, $Originator, $Text) {
        $this->MessageID = $MessageID;
        $this->Originator = $Originator;
        $this->Text = $Text;
    }

    /**
     * @return mixed
     */
    public function getMessageID()
    {
        return $this->MessageID;
    }

    /**
     * @param mixed $MessageID
     */
    public function setMessageID($MessageID)
    {
        $this->MessageID = $MessageID;
    }

    /**
     * @return mixed
     */
    public function getOriginator()
    {
        return $this->Originator;
    }

    /**
     * @param mixed $Originator
     */
    public function setOriginator($Originator)
    {
        $this->Originator = $Originator;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->Text;
    }

    /**
     * @param mixed $Text
     */
    public function setText($Text)
    {
        $this->Text = $Text;
    }

    public static function getMessages() {
        $messagesQuery = 'SELECT message_id, originator, message_text
                          FROM gossip_messages';
        $stmt = Database::getDB()->prepare($messagesQuery);
        $stmt->execute();
        $messageResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // error_log('[gossip][User][getMessages]::$messageResults ' . print_r($messageResults, true));

        $messages = array();
        foreach ($messageResults as $message) {
            // error_log('[gossip][User][getMessages]::$user ' . print_r($user, true));
            $messages[] = new ChatMessage($message['message_id'], $message['originator'], $message['message_text']);
        }
        // error_log('[gossip][User][getMessages]::$messages ' . print_r($messages, true));
        return $messages;
    }

    public static function saveMessage($order, $originator, $text) {
        $node = ChatNode::build($_SERVER['HTTP_HOST']);
        $messageId = $node->getNodeId() . ':' . $order;
    }
}