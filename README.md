JSON Describer
=============
By Weng Fei Fung

What is this
---
Describe your javascript object, json, or database by converting it to an expandable tree. You can provide explanations with links to open and jump to a part of the tree.        
        
How to setup
---
1. Provide a path to the JSON file in the URL ?json=. 

    - Your JSON is in the JSON file.
    - If you are describing a javascript object, first convert that with JSON.stringify and place into the file. 
    - If you are describing a mySQL database, export from phpMyAdmin as a JSON file (not a sql file).

    Reminder: Starting and ending with square brackets is still valid json. Make sure the last object does not end with a comma. For example, [ {..}, {..} ]

2. Since you are just describing, you may not want to be exhaustive with the data. For example, your databse you exported may have many rows. Feel free to delete some objects in an array, making sure the last object does not end with a comma. A good number of rows is 10 or less.

    Reminder: If you modify the JSON file, this is a good online service to check of mistakes, including unnecessary comma:
    https://jsonlint.com/

    You can preview how a tree would look like here (notice it does not check exhaustively for mistakes, ignoring unnecessary comma's):
    https://jsoneditoronline.org/ 
3. You can describe the contents of the json file. Add a path to the text, html, or php file in the URL ?desc=
   HTML code is allowed. You can open a part of the tree during your explanation by providing a link to goToMatched(<String here>). You provide the string that a key or value is matched against. For example:
   ``<a href='#' onclick='goToMatched("John Doe");'>Go to all object(s) that has key or value: John Doe</a>``

   You can match for a string AND another string in case there are multiple John Doe objects and you want to narrow down the John Doe object. The second parameter is optional for more narrowed matching.
   ``<a href='#' onclick='goToMatched("Jane Doe", "Los Angeles");'>Go to all object(s) that has both those two keys or values (Jane Doe, Los Angeles).</a>``

   Furthermore, you can use regular expression(s) too!
   ``<a href='#' onclick="goToMatched(/[zZ]/)">Go to object(s) that has a key or value with the letter Z in it, using regular expression.</a>``