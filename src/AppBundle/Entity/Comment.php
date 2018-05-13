<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255)
     */
    private $comment;

    /**
     * @var int
     *
     * @ORM\Column(name="issue_id", type="integer", nullable=true)
     */
    private $issueId;

    /**
     * @var int
     *
     * @ORM\Column(name="project_id", type="integer", nullable=true)
     */
    private $projectId;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string")
     */
    private $userName;


    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Comment
     */
    public function setComment($comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment():? string
    {
        return $this->comment;
    }

    /**
     * Set issueId
     *
     * @param integer $issueId
     *
     * @return Comment
     */
    public function setIssueId($issueId):self
    {
        $this->issueId = $issueId;

        return $this;
    }

    /**
     * Get issueId
     *
     * @return int
     */
    public function getIssueId(): int
    {
        return $this->issueId;
    }

    /**
     * Set projectId
     *
     * @param integer $projectId
     *
     * @return Comment
     */
    public function setProjectId($projectId): self
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Get projectId
     *
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->projectId;
    }

    /**
     * Set user name
     *
     * @param string $userName
     *
     * @return Comment
     */
    public function setUserName($userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getUserName():? string
    {
        return $this->userName;
    }
}

