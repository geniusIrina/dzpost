<?php

function add(float $arg1,float $arg2): float 
{
    return $arg1 + $arg2;
}

function subtract($arg1, $arg2) 
{
    return $arg1 - $arg2;
}

function multiply($arg1, $arg2) 
{
    return $arg1 * $arg2;
}

function divide($arg1, $arg2): float|string 
{
    return ($arg2 === 0)? "Ошибка: деление на ноль.": $arg1 / $arg2;
}

function calculate(string $operation, $arg1, $arg2) 
{
    switch ($operation) {
        case '+':
            return add($arg1, $arg2);
        case '-':
            return subtract($arg1, $arg2);
        case '*':
            return multiply($arg1, $arg2);
        case '/':
            return divide($arg1, $arg2);
        default:
            return "Ошибка: неизвестная операция.";
    }
}