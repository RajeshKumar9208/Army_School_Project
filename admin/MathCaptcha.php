<?php
// File: MathCaptcha.php
class MathCaptcha {
    private $num1;
    private $num2;
    private $operator;
    
    public function __construct() {
        $this->num1 = rand(1, 10);
        $this->num2 = rand(1, 10);
        $operators = ['+', '-', '*'];
        $this->operator = $operators[array_rand($operators)];
    }
    
    public function getQuestion() {
        return "What is {$this->num1} {$this->operator} {$this->num2}?";
    }
    
    public function getAnswer() {
        switch ($this->operator) {
            case '+': return $this->num1 + $this->num2;
            case '-': return $this->num1 - $this->num2;
            case '*': return $this->num1 * $this->num2;
            default: return 0; // Should never happen
        }
    }
    
    public function validate($answer) {
        return (int)$answer == $this->getAnswer();
    }
}
?>