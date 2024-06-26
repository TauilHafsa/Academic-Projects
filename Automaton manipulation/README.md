# Project description: Automation manipulation

We aim to develop a program capable of reading and storing in memory an automaton A from a .txt file. Subsequently, we will implement some basic algorithms for automaton manipulation.

## Part 1:
- Propose a data structure to store the information of an automaton.
- Add a function to read and store an automaton from a .txt file; each line of this file is structured as follows:
     starting_state     destination_state   transition_label

     Initial and final states are provided at the end of the file.
- Add a function to display the set of alphabet.

## Part 2:
- Add a function to generate a .dot file associated with an automaton passed as a parameter and display on the screen the corresponding .png file.

     The initial state should be colored green, final states in blue, unreachable states in gray, and other states in black.

## Part 3:
- Add a function to test if a word M is generated by an automaton A. The word M and the automaton A are parameters of this function.
- Add a function to read the content of the file Words.txt and display the list of words generated by the automaton A passed as a parameter.

## Part 4:
- Add the following functions:
  
     Union_Automata: to calculate the union of automata passed as parameters.

     Automaton_Star: to star an automaton passed as a parameter.

     Product_Automata: to calculate the product of two automata passed as parameters.

## Part 5:
- Add a function to remove epsilon transitions from an automaton passed as a parameter.
- Add a function to return a deterministic automaton associated with an automaton passed as a parameter.
- Add a function to return a minimal automaton associated with an automaton passed as a parameter.
