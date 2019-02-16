<div>
    Let's go to an <a href='#' onclick='goToMatched("John Doe");'>individual object by matching the name: John Doe.</a> Check out the link's html to figure out how to do this.
</div>

<div>
    Let's go to an <a href='#' onclick='goToMatched("18");'>individual object by matching: 18.</a>
</div>

<div>
    Let's go to an <a href='#' onclick='goToMatched("name");'>individual object by matching the key: name.</a>
</div>

<div>
    Let's go to an <a href='#' onclick='goToMatched("Jane Doe", "Los Angeles");'>individual object that are matched against two strings (Jane Doe, Los Angeles).</a>
</div>

<div>
    Let's go to an <a href='#' onclick="goToMatched(/[zZ]/)">individual object that has the the letter Z in their name, using regular expression.</a> Note that reg exp search is limited such as no ability to do negative lookahead. Last resort, you may add your own custom key/value into the json file for the purpose of jumping to that part of the tree.