<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            // JavaScript Questions
            [
                'question' => 'What is the output of: console.log(typeof typeof 1)?',
                'options' => ['number', 'string', 'undefined', 'object'],
                'correct_answer' => 'string',
                'language' => 'JavaScript',
            ],
            [
                'question' => 'What is closure in JavaScript?',
                'options' => [
                    'A function that returns another function',
                    'A function that has access to variables in its outer scope',
                    'A way to close a browser window',
                    'A method to end a JavaScript program'
                ],
                'correct_answer' => 'A function that has access to variables in its outer scope',
                'language' => 'JavaScript',
            ],
            [
                'question' => 'What does the "this" keyword refer to in JavaScript?',
                'options' => [
                    'The current function',
                    'The global object',
                    'The object that owns the method',
                    'The parent object'
                ],
                'correct_answer' => 'The object that owns the method',
                'language' => 'JavaScript',
            ],
            [
                'question' => 'What is the difference between == and === in JavaScript?',
                'options' => [
                    '== checks value, === checks value and type',
                    '== checks type, === checks value',
                    'They are identical',
                    '== is strict, === is loose'
                ],
                'correct_answer' => '== checks value, === checks value and type',
                'language' => 'JavaScript',
            ],
            [
                'question' => 'What is a Promise in JavaScript?',
                'options' => [
                    'A function that executes immediately',
                    'An object representing the eventual completion of an async operation',
                    'A way to loop through arrays',
                    'A method to create variables'
                ],
                'correct_answer' => 'An object representing the eventual completion of an async operation',
                'language' => 'JavaScript',
            ],
            [
                'question' => 'What is the purpose of Array.map()?',
                'options' => [
                    'To filter array elements',
                    'To transform each element and return a new array',
                    'To sort array elements',
                    'To remove duplicate elements'
                ],
                'correct_answer' => 'To transform each element and return a new array',
                'language' => 'JavaScript',
            ],
            [
                'question' => 'What is hoisting in JavaScript?',
                'options' => [
                    'Moving variables to the top of their scope',
                    'Raising an error',
                    'A way to declare variables',
                    'A method to hide variables'
                ],
                'correct_answer' => 'Moving variables to the top of their scope',
                'language' => 'JavaScript',
            ],
            [
                'question' => 'What does "use strict" do in JavaScript?',
                'options' => [
                    'Enables strict mode with stricter error checking',
                    'Disables error checking',
                    'Forces synchronous execution',
                    'Enables async/await'
                ],
                'correct_answer' => 'Enables strict mode with stricter error checking',
                'language' => 'JavaScript',
            ],
            [
                'question' => 'What is the spread operator (...) used for?',
                'options' => [
                    'To combine arrays and objects',
                    'To split arrays and objects',
                    'To expand iterables into individual elements',
                    'To create loops'
                ],
                'correct_answer' => 'To expand iterables into individual elements',
                'language' => 'JavaScript',
            ],
            [
                'question' => 'What is the difference between let, const, and var?',
                'options' => [
                    'let is block-scoped, const is constant, var is function-scoped',
                    'They are all identical',
                    'let is for loops, const is for arrays, var is for objects',
                    'let is global, const is local, var is block-scoped'
                ],
                'correct_answer' => 'let is block-scoped, const is constant, var is function-scoped',
                'language' => 'JavaScript',
            ],

            // Python Questions
            [
                'question' => 'What is the difference between list and tuple in Python?',
                'options' => [
                    'Lists are mutable, tuples are immutable',
                    'Tuples are mutable, lists are immutable',
                    'They are identical',
                    'Lists are faster than tuples'
                ],
                'correct_answer' => 'Lists are mutable, tuples are immutable',
                'language' => 'Python',
            ],
            [
                'question' => 'What is a list comprehension in Python?',
                'options' => [
                    'A way to read files',
                    'A concise way to create lists',
                    'A method to delete lists',
                    'A way to sort lists'
                ],
                'correct_answer' => 'A concise way to create lists',
                'language' => 'Python',
            ],
            [
                'question' => 'What is the purpose of __init__ in Python?',
                'options' => [
                    'To initialize a class instance',
                    'To terminate a program',
                    'To import modules',
                    'To create a function'
                ],
                'correct_answer' => 'To initialize a class instance',
                'language' => 'Python',
            ],
            [
                'question' => 'What is the difference between == and is in Python?',
                'options' => [
                    '== compares values, is compares identity',
                    'is compares values, == compares identity',
                    'They are identical',
                    '== is for strings, is is for numbers'
                ],
                'correct_answer' => '== compares values, is compares identity',
                'language' => 'Python',
            ],
            [
                'question' => 'What is a decorator in Python?',
                'options' => [
                    'A function that modifies another function',
                    'A way to comment code',
                    'A method to delete functions',
                    'A type of variable'
                ],
                'correct_answer' => 'A function that modifies another function',
                'language' => 'Python',
            ],
            [
                'question' => 'What is a generator in Python?',
                'options' => [
                    'A function that returns an iterator',
                    'A way to generate random numbers',
                    'A method to create classes',
                    'A type of loop'
                ],
                'correct_answer' => 'A function that returns an iterator',
                'language' => 'Python',
            ],
            [
                'question' => 'What does *args do in Python?',
                'options' => [
                    'Allows passing a variable number of arguments',
                    'Multiplies arguments',
                    'Removes arguments',
                    'Sorts arguments'
                ],
                'correct_answer' => 'Allows passing a variable number of arguments',
                'language' => 'Python',
            ],
            [
                'question' => 'What is the Global Interpreter Lock (GIL) in Python?',
                'options' => [
                    'A mechanism that allows only one thread to execute at a time',
                    'A way to lock files',
                    'A method to prevent errors',
                    'A type of variable'
                ],
                'correct_answer' => 'A mechanism that allows only one thread to execute at a time',
                'language' => 'Python',
            ],
            [
                'question' => 'What is the difference between append() and extend() in Python lists?',
                'options' => [
                    'append() adds one element, extend() adds multiple elements',
                    'extend() adds one element, append() adds multiple elements',
                    'They are identical',
                    'append() removes elements, extend() adds elements'
                ],
                'correct_answer' => 'append() adds one element, extend() adds multiple elements',
                'language' => 'Python',
            ],
            [
                'question' => 'What is a lambda function in Python?',
                'options' => [
                    'An anonymous function',
                    'A named function',
                    'A method to import modules',
                    'A type of class'
                ],
                'correct_answer' => 'An anonymous function',
                'language' => 'Python',
            ],

            // Java Questions
            [
                'question' => 'What is the difference between == and .equals() in Java?',
                'options' => [
                    '== compares references, .equals() compares values',
                    '.equals() compares references, == compares values',
                    'They are identical',
                    '== is for strings, .equals() is for numbers'
                ],
                'correct_answer' => '== compares references, .equals() compares values',
                'language' => 'Java',
            ],
            [
                'question' => 'What is method overriding in Java?',
                'options' => [
                    'Providing a specific implementation of a method in a subclass',
                    'Creating a new method',
                    'Deleting a method',
                    'Renaming a method'
                ],
                'correct_answer' => 'Providing a specific implementation of a method in a subclass',
                'language' => 'Java',
            ],
            [
                'question' => 'What is the difference between abstract class and interface in Java?',
                'options' => [
                    'Abstract class can have implemented methods, interface cannot',
                    'Interface can have implemented methods, abstract class cannot',
                    'They are identical',
                    'Abstract class is for variables, interface is for methods'
                ],
                'correct_answer' => 'Abstract class can have implemented methods, interface cannot',
                'language' => 'Java',
            ],
            [
                'question' => 'What is the purpose of finally block in Java?',
                'options' => [
                    'To execute code regardless of exceptions',
                    'To catch exceptions',
                    'To throw exceptions',
                    'To prevent exceptions'
                ],
                'correct_answer' => 'To execute code regardless of exceptions',
                'language' => 'Java',
            ],
            [
                'question' => 'What is polymorphism in Java?',
                'options' => [
                    'The ability of objects to take multiple forms',
                    'A way to create variables',
                    'A method to delete objects',
                    'A type of loop'
                ],
                'correct_answer' => 'The ability of objects to take multiple forms',
                'language' => 'Java',
            ],
            [
                'question' => 'What is the difference between String, StringBuilder, and StringBuffer?',
                'options' => [
                    'String is immutable, StringBuilder and StringBuffer are mutable',
                    'StringBuilder is immutable, String and StringBuffer are mutable',
                    'They are all identical',
                    'String is thread-safe, StringBuilder and StringBuffer are not'
                ],
                'correct_answer' => 'String is immutable, StringBuilder and StringBuffer are mutable',
                'language' => 'Java',
            ],
            [
                'question' => 'What is garbage collection in Java?',
                'options' => [
                    'Automatic memory management',
                    'Manual memory deletion',
                    'A way to create objects',
                    'A method to sort arrays'
                ],
                'correct_answer' => 'Automatic memory management',
                'language' => 'Java',
            ],
            [
                'question' => 'What is the difference between ArrayList and LinkedList?',
                'options' => [
                    'ArrayList uses array, LinkedList uses nodes',
                    'LinkedList uses array, ArrayList uses nodes',
                    'They are identical',
                    'ArrayList is faster for insertions, LinkedList is faster for access'
                ],
                'correct_answer' => 'ArrayList uses array, LinkedList uses nodes',
                'language' => 'Java',
            ],
            [
                'question' => 'What is a static method in Java?',
                'options' => [
                    'A method that belongs to the class, not instances',
                    'A method that cannot be called',
                    'A method that changes frequently',
                    'A method that only works with strings'
                ],
                'correct_answer' => 'A method that belongs to the class, not instances',
                'language' => 'Java',
            ],
            [
                'question' => 'What is the purpose of super keyword in Java?',
                'options' => [
                    'To refer to parent class members',
                    'To create new objects',
                    'To delete objects',
                    'To prevent inheritance'
                ],
                'correct_answer' => 'To refer to parent class members',
                'language' => 'Java',
            ],
        ];

        foreach ($questions as $question) {
            Question::create($question);
        }
    }
}
