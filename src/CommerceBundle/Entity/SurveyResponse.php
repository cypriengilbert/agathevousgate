<?php

namespace CommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SurveyResponse
 *
 * @ORM\Table(name="survey_response")
 * @ORM\Entity(repositoryClass="CommerceBundle\Repository\SurveyResponseRepository")
 */
class SurveyResponse
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
     * @ORM\Column(name="question1", type="string", length=255)
     */
    private $question1;

    /**
     * @var string
     *
     * @ORM\Column(name="response1", type="string", length=255)
     */
    private $response1;
      /**
     * @var string
     *
     * @ORM\Column(name="question2", type="string", length=255)
     */
     private $question2;
     
         /**
          * @var string
          *
          * @ORM\Column(name="response2", type="string", length=255)
          */
         private $response2;

             /**
     * @var string
     *
     * @ORM\Column(name="question3", type="string", length=255)
     */
     private $question3;
     
         /**
          * @var string
          *
          * @ORM\Column(name="response3", type="string", length=255)
          */
         private $response3;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255)
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set question
     *
     * @param string $question
     *
     * @return SurveyResponse
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set response
     *
     * @param string $response
     *
     * @return SurveyResponse
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return SurveyResponse
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return SurveyResponse
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set question1
     *
     * @param string $question1
     *
     * @return SurveyResponse
     */
    public function setQuestion1($question1)
    {
        $this->question1 = $question1;

        return $this;
    }

    /**
     * Get question1
     *
     * @return string
     */
    public function getQuestion1()
    {
        return $this->question1;
    }

    /**
     * Set response1
     *
     * @param string $response1
     *
     * @return SurveyResponse
     */
    public function setResponse1($response1)
    {
        $this->response1 = $response1;

        return $this;
    }

    /**
     * Get response1
     *
     * @return string
     */
    public function getResponse1()
    {
        return $this->response1;
    }

    /**
     * Set question2
     *
     * @param string $question2
     *
     * @return SurveyResponse
     */
    public function setQuestion2($question2)
    {
        $this->question2 = $question2;

        return $this;
    }

    /**
     * Get question2
     *
     * @return string
     */
    public function getQuestion2()
    {
        return $this->question2;
    }

    /**
     * Set response2
     *
     * @param string $response2
     *
     * @return SurveyResponse
     */
    public function setResponse2($response2)
    {
        $this->response2 = $response2;

        return $this;
    }

    /**
     * Get response2
     *
     * @return string
     */
    public function getResponse2()
    {
        return $this->response2;
    }

    /**
     * Set question3
     *
     * @param string $question3
     *
     * @return SurveyResponse
     */
    public function setQuestion3($question3)
    {
        $this->question3 = $question3;

        return $this;
    }

    /**
     * Get question3
     *
     * @return string
     */
    public function getQuestion3()
    {
        return $this->question3;
    }

    /**
     * Set response3
     *
     * @param string $response3
     *
     * @return SurveyResponse
     */
    public function setResponse3($response3)
    {
        $this->response3 = $response3;

        return $this;
    }

    /**
     * Get response3
     *
     * @return string
     */
    public function getResponse3()
    {
        return $this->response3;
    }
}
