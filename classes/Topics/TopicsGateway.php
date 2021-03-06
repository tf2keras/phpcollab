<?php


namespace phpCollab\Topics;

use phpCollab\Database;

/**
 * Class TopicsGateway
 * @package phpCollab\Topics
 */
class TopicsGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * Topics constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $topicId
     * @param $memberId
     * @param $message
     * @param $created
     * @return string
     */
    public function createPost($topicId, $memberId, $message, $created)
    {
        $query = "INSERT INTO {$this->tableCollab["posts"]} (topic, member, created, message) VALUES (:topic, :member, :created, :message)";
        $this->db->query($query);
        $this->db->bind(":topic", $topicId);
        $this->db->bind(":member", $memberId);
        $this->db->bind(":message", $message);
        $this->db->bind(":created", $created);
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    /**
     * @param $projectId
     * @param $memberId
     * @param $subject
     * @param $status
     * @param $last_post
     * @param $posts
     * @param $published
     * @return string
     */
    public function createTopic($projectId, $memberId, $subject, $status, $last_post, $posts, $published)
    {

        $query = "INSERT INTO {$this->tableCollab["topics"]} (project, owner, subject, status, last_post, posts, published) VALUES (:project, :owner, :subject, :status, :last_post, :posts, :published)";
        $this->db->query($query);
        $this->db->bind(":project", $projectId);
        $this->db->bind(":owner", $memberId);
        $this->db->bind(":subject", $subject);
        $this->db->bind(":status", $status);
        $this->db->bind(":last_post", $last_post);
        $this->db->bind(":posts", $posts);
        $this->db->bind(":published", $published);
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    /**
     * @param $ownerId
     * @param null $sorting
     * @return mixed
     */
    public function getTopicsByOwner($ownerId, $sorting = null)
    {
        $query = $this->initrequest["topics"] . " WHERE topic.project = :owner_id" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':owner_id', $ownerId);
        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @param null $offset
     * @param null $limit
     * @param null $sorting
     * @return mixed
     */
    public function getTopicsByProject($projectId, $offset = null, $limit = null, $sorting = null)
    {
        $query = $this->initrequest["topics"] . " WHERE topic.project = :project_id" . $this->orderBy($sorting) . $this->limit($offset, $limit);
        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @param null $offset
     * @param null $limit
     * @param null $sorting
     * @return mixed
     */
    public function getProjectSiteTopics($projectId, $offset = null, $limit = null, $sorting = null)
    {
        $query = $this->initrequest["topics"] .  " WHERE topic.project = :project_id AND topic.published = 0" . $this->orderBy($sorting) . $this->limit($offset, $limit);
        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $topicId
     * @return mixed
     */
    public function getTopicById($topicId)
    {
        $query = $this->initrequest["topics"] . " WHERE topic.id = :topic_id";
        $this->db->query($query);
        $this->db->bind(':topic_id', $topicId);
        return $this->db->single();
    }

    /**
     * @param $topicIds
     * @return mixed
     */
    public function getTopicsIn($topicIds)
    {
        $topicIds = explode(',', $topicIds);
        $placeholders = str_repeat('?, ', count($topicIds) - 1) . '?';
        $whereStatement = " WHERE topic.id IN($placeholders)";
        $this->db->query($this->initrequest["topics"] . $whereStatement);
        $this->db->execute($topicIds);
        return $this->db->resultset();
    }

    /**
     * @param $topicId
     * @return mixed
     */
    public function getPostsByTopicId($topicId)
    {
        $query = $this->initrequest["posts"] . " WHERE pos.topic = :topic_id ORDER BY pos.created DESC";
        $this->db->query($query);
        $this->db->bind(':topic_id', $topicId);
        return $this->db->resultset();
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function getPostById($postId)
    {
        $query = $this->initrequest["posts"] . " WHERE pos.id = :post_id";
        $this->db->query($query);
        $this->db->bind(':post_id', $postId);
        return $this->db->single();
    }

    /**
     * @param $topicId
     * @param $ownerId
     * @return mixed
     */
    public function getPostsByTopicIdAndNotOwner($topicId, $ownerId)
    {
        $query = $this->initrequest["posts"] . " WHERE pos.topic = :topic_id AND pos.member != :owner_id ORDER BY mem.id";
        $this->db->query($query);
        $this->db->bind(':topic_id', $topicId);
        $this->db->bind(':owner_id', $ownerId);
        return $this->db->resultset();
    }

    /**
     * @param $projectIds
     * @param $dateFilter
     * @param $sorting
     * @return mixed
     */
    public function getTopicsByProjectAndFilteredByDate($projectIds, $dateFilter, $sorting)
    {
        $projectId = explode(',', $projectIds);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $where = " WHERE topic.project IN($placeholders) AND topic.last_post > ? AND topic.status = 1";

        $query = $this->initrequest["topics"] . $where . $this->orderBy($sorting);
        array_push($projectId, $dateFilter);
        $this->db->query($query);
        $this->db->execute($projectId);
        return $this->db->fetchAll();
    }

    /**
     * @param $topicIds
     * @return mixed
     * @internal param string $table
     */
    public function publishTopic($topicIds) {
        if ( strpos($topicIds, ',') ) {
            $topicIds = explode(',', $topicIds);
            $placeholders = str_repeat ('?, ', count($topicIds)-1) . '?';
            $sql = "UPDATE {$this->tableCollab['topics']} SET published = 0 WHERE id IN ($placeholders)";
            $this->db->query($sql);

            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE {$this->tableCollab['topics']} SET published = 0 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }

    /**
     * @param $topicIds
     * @return mixed
     * @internal param string $table
     */
    public function unPublishTopic($topicIds) {
        if ( strpos($topicIds, ',') ) {
            $topicIds = explode(',', $topicIds);
            $placeholders = str_repeat ('?, ', count($topicIds)-1) . '?';
            $sql = "UPDATE {$this->tableCollab['topics']} SET published = 1 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE {$this->tableCollab['topics']} SET published = 1 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }


    /**
     * @param $topicIds
     * @return mixed
     * @internal param $table
     */
    public function closeTopic($topicIds) {
        if ( strpos($topicIds, ',') ) {
            $topicIds = explode(',', $topicIds);
            $placeholders = str_repeat ('?, ', count($topicIds)-1) . '?';
            $sql = "UPDATE {$this->tableCollab['topics']} SET status=0 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE {$this->tableCollab['topics']} SET status=0 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }

    /**
     * @param $topicIds
     * @return mixed
     */
    public function deleteTopics($topicIds)
    {
        // Generate placeholders
        $placeholders = str_repeat('?, ', count($topicIds) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['topics']} WHERE id IN ($placeholders)";
        $this->db->query($sql);

        return $this->db->execute($topicIds);
    }

    /**
     * @param $topicIds
     * @return mixed
     */
    public function deletePostsByTopicIds($topicIds)
    {
        // Generate placeholders
        $placeholders = str_repeat('?, ', count($topicIds) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['posts']} WHERE topic IN ($placeholders)";
        $this->db->query($sql);

        return $this->db->execute($topicIds);
    }
    
    /**
     * @param $projectId
     * @return mixed
     */
    public function deleteTopicsByProjectId($projectId)
    {
        $projectId = explode(',', $projectId);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['topics']} WHERE project IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectId);
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function deletePostsByProjectId($projectId)
    {
        $projectId = explode(',', $projectId);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['posts']} WHERE topic IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectId);
    }

    /**
     * @param $topicId
     * @param $updateDate
     * @return mixed
     */
    public function incrementTopicPostsCount($topicId, $updateDate)
    {
        $query = "UPDATE {$this->tableCollab["topics"]} SET last_post = :last_post, posts = posts + 1 WHERE id = :topic_id";
        $this->db->query($query);
        $this->db->bind(":last_post", $updateDate);
        $this->db->bind(":topic_id", $topicId);
        return $this->db->execute();
    }

    /**
     * @param $topicId
     * @param $updateDate
     * @return mixed
     */
    public function decrementTopicPostsCount($topicId, $updateDate)
    {
        $query = "UPDATE {$this->tableCollab["topics"]} SET last_post = :last_post, posts = posts - 1 WHERE id = :topic_id";
        $this->db->query($query);
        $this->db->bind(":last_post", $updateDate);
        $this->db->bind(":topic_id", $topicId);
        return $this->db->execute();
    }

    /**
     * @param $query
     * @param null $sorting
     * @param null $limit
     * @param null $rowLimit
     * @return mixed
     */
    public function searchResultTopics($query, $sorting = null, $limit = null, $rowLimit = null)
    {
        $sql = $this->initrequest['topics'] . ' ' . $query . $this->orderBy($sorting) . $this->limit($limit, $rowLimit);
        $this->db->query($sql);
        $this->db->execute();
        return $this->db->resultset();
    }

    /**
     * Returns the LIMIT attribute for SQL strings
     * @param $start
     * @param $rowLimit
     * @return string
     */
    private function limit($start, $rowLimit)
    {
        if (!is_null($start) && !is_null($rowLimit)) {
            return " LIMIT {$start},{$rowLimit}";
        }
        return '';
    }

    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["topic.id","topic.project","topic.owner","topic.subject","topic.status","topic.last_post","topic.posts","topic.published","mem.id","mem.login","mem.name","mem.email_work","pro.id","pro.name"];
            $pieces = explode(' ', $sorting);

            if ($pieces) {
                $key = array_search($pieces[0], $allowedOrderedBy);

                if ($key !== false) {
                    $order = $allowedOrderedBy[$key];
                    return " ORDER BY $order $pieces[1]";
                }
            }
        }

        return '';
    }
}
