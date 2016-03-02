<?php

/**
 * Created by PhpStorm.
 * User: Andrew
 * Date: 3/1/2016
 * Time: 9:10 PM
 */
class ChatMessage
{
    protected $Originator;
    protected $Text;
    protected $messageUUID;
    protected $messageOrder;

    public function __construct($MessageID, $Originator, $Text) {
        $this->Originator = $Originator;
        $this->Text = $Text;
        self::parseMessageID($MessageID);
    }

    private function parseMessageID($MessageID) {
        list($this->messageUUID, $this->messageOrder) = explode(':', $MessageID);
    }

    private static function formatMessageID($messageUUID, $messageOrder) {
        return $messageUUID . ':' . $messageOrder;
    }

    /**
     * @return mixed
     */
    public function getMessageID()
    {
        return self::formatMessageID($this->messageUUID, $this->messageOrder);
    }

    /**
     * @param mixed $MessageID
     */
    public function setMessageID($MessageID)
    {
        self::parseMessageID($MessageID);
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

    /**
     * @return mixed
     */
    public function getMessageUUID()
    {
        return $this->messageUUID;
    }

    /**
     * @param mixed $messageUUID
     */
    public function setMessageUUID($messageUUID)
    {
        $this->messageUUID = $messageUUID;
    }

    /**
     * @return mixed
     */
    public function getMessageOrder()
    {
        return $this->messageOrder;
    }

    /**
     * @param mixed $messageOrder
     */
    public function setMessageOrder($messageOrder)
    {
        $this->messageOrder = $messageOrder;
    }

    public function toArray() {
        return array('MessageID'  => $this->getMessageID(),
                     'Originator' => $this->getOriginator(),
                     'Text'       => $this->getText(),
            );
    }

    public static function getMessages() {
        $messagesQuery = 'SELECT message_uuid, message_order, originator, message_text
                          FROM gossip_messages';
        $stmt = Database::getDB()->prepare($messagesQuery);
        $stmt->execute();
        $messageResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // error_log('[gossip][ChatMessage][getMessages]::$messageResults ' . print_r($messageResults, true));

        $messages = array();
        foreach ($messageResults as $message) {
            // error_log('[gossip][ChatMessage][getMessages]::$user ' . print_r($user, true));
            $messageID = self::formatMessageID($message['message_uuid'], $message['message_order']);
            $messages[] = new ChatMessage($messageID, $message['originator'], $message['message_text']);
        }
        // error_log('[gossip][ChatMessage][getMessages]::$messages ' . print_r($messages, true));
        return $messages;
    }

    public static function saveLocalMessage($order, $originator, $text) {
        $node = LocalNode::getLocalNode();
        $messageUUID = $node->getNodeId();

        $messagesQuery = 'INSERT INTO gossip_messages(message_uuid, message_order, originator, message_text)
                          VALUES (:messageUUID, :messageOrder, :originator, :text)';
        $stmt = Database::getDB()->prepare($messagesQuery);
        $stmt->bindValue('messageUUID', $messageUUID);
        $stmt->bindValue('messageOrder', $order);
        $stmt->bindValue('originator', $originator);
        $stmt->bindValue('text', $text);
        $stmt->execute();

        return self::getMessages();
    }

    public static function pollForMessages() {

    }
}