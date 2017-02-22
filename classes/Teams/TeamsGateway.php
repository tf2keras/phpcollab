<?php


namespace phpCollab\Teams;

use phpCollab\Database;

/**
 * Class TeamsGateway
 * @package phpCollab\Teams
 */
class TeamsGateway
{
    protected $db;
    protected $initrequest;

    /**
     * TeamsGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
    }

    /**
     * @param $projectId
     * @param $memberId
     * @return mixed
     */
    public function getTeamByProjectIdAndTeamMember($projectId, $memberId)
    {
        $whereStatement = " WHERE tea.project = :project_id AND tea.member = :member_id";
        $this->db->query($this->initrequest["teams"] . $whereStatement);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':member_id', $memberId);
        $results = $this->db->resultset();
        return $results;
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getTeamByMemberId($memberId)
    {
        //$tmpquery = "WHERE tea.member = '$idSession' ORDER BY pro.name";
        $whereStatement = " WHERE tea.member = :member_id ORDER BY pro.name";
        $this->db->query($this->initrequest["teams"] . $whereStatement);
        $this->db->bind(':member_id', $memberId);
        $results = $this->db->resultset();
        return $results;
    }

    /**
     * @param $memberId
     * @param $orgId
     * @return mixed
     */
    public function getTeamByTeamMemberAndOrgId($memberId, $orgId)
    {
        $whereStatement = "WHERE tea.member = :member_id AND org2.id = :org_id";
        $this->db->query($this->initrequest["teams"] . $whereStatement);
        $this->db->bind(':org_id', $orgId);
        $this->db->bind(':member_id', $memberId);
        $results = $this->db->resultset();
        return $results;
    }

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     * @internal param $orderBy
     */
    public function getTeamByProjectIdAndOrderBy($projectId, $sorting = null)
    {
        $whereStatement = " WHERE tea.project = :project_id";

        $sql = $this->initrequest["teams"] . $whereStatement . $this->orderBy($sorting);

        $this->db->query($sql);
        $this->db->bind(':project_id', $projectId);
        $results = $this->db->resultset();

        return $results;
    }

    /**
     * @param $projectId
     * @param $memberId
     * @return mixed
     */
    public function isTeamMember($projectId, $memberId)
    {
        $whereStatement = " WHERE tea.project = :project_id AND tea.member = :member_id";

        $sql = $this->initrequest["teams"] . $whereStatement;

        $this->db->query($sql);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':member_id', $memberId);
        return $this->db->resultset();
    }

    public function deleteFromTeamsWhereProjectIdEqualsAndMemberIdIn($projectId, $memberId)
    {
        // Generate placeholders
        $placeholders = str_repeat ('?, ', count($memberId)-1) . '?';

        $sql = "DELETE FROM {$this->tableCollab["teams"]} WHERE project = ? AND member IN($placeholders)";

        // Prepend the project id value
        array_unshift($placeholders,$projectId);
        $this->db->query($sql);
        $this->db->execute($placeholders);
        return $this->db->fetchAll();


    }

    /**
     * @param $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = [];
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
