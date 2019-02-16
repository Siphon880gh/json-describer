<!DOCTYPE html>
<html lang="en">
  <head>
   <title>Json Tree</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

    <!-- jQuery and Bootstrap  -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
        
    <!-- Render Json (to expandable Tree)-->
    <script src="https://cdn.rawgit.com/caldwell/renderjson/master/renderjson.js"></script>

    <script>
    // Replace jQuery :contains with case-insensitive version
    jQuery.expr[':'].Contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase()
        .indexOf(m[3].toUpperCase()) >= 0;
    };
    // Add jQuery :regex that allows regular expression queries
    jQuery.expr[':'].regex = function(a,i,m) {
        var regreg =  /^\/((?:\\\/|[^\/])+)\/([mig]{0,3})$/,
        reg = regreg.exec(m[3]);
        return reg ? RegExp(reg[1], reg[2]).test($.trim(a.innerHTML)) : false;
    }
    
    $(()=>{
        console.log(`What is this:
Describe your javascript object, json, or database by converting it to an expandable tree. You can provide explanations with links to open and jump to a part of the tree.        
        
How to setup:
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
   <a href='#' onclick='goToMatched("John Doe");'>Go to all object(s) that has key or value: John Doe</a>

   You can match for a string AND another string in case there are multiple John Doe objects and you want to narrow down the John Doe object. The second parameter is optional for more narrowed matching.
   <a href='#' onclick='goToMatched("Jane Doe", "Los Angeles");'>Go to all object(s) that has both those two keys or values (Jane Doe, Los Angeles).</a>
    
   Furthermore, you can use regular expression(s) too!
   <a href='#' onclick="goToMatched(/[zZ]/)">Go to object(s) that has a key or value with the letter Z in it, using regular expression.</a>
`);
        const jsonFile = "<?php if(isset($_GET["json"])) echo $_GET["json"]; else echo "files/samples/a.json"; ?>";
        const descFile = "<?php if(isset($_GET["desc"])) echo $_GET["desc"]; else echo "files/samples/a-desc.php"; ?>";

        $.getJSON("get-json.php", {json:`${jsonFile}`})
            .done(whenReadyJSON);

        $.get("get-desc.php", {desc:`${descFile}`})
            .done(whenReadyDesc);

        function whenReadyJSON(json) {
            // Render tree:
            var $dom = renderjson.set_show_to_level("all")(json);
            $("#json").append($dom);

            var intv = setInterval( ()=> {

                if($(".renderjson").length>0) {
                    clearInterval(intv);
                    $(".renderjson a").click();
                    $('[style^="display"]').addClass("gotoable");
                }
                // setTimeout( ()=> {
                //     clearInterval(intv);
                //     // $(".renderjson a").click();
                //     // $('[style^="display"]').addClass("gotoable");
                // }, 2000);
            }, 10);
        } // whenReadyJSON

        function whenReadyDesc(txt) {
            $("#desc").html(txt);
        } // whenReadyJSON
            
    });

    /**
     * @param {string} str: Match string against key or value from all objects. Show only the objects that matched.
     * @param {string} str2: Optional. This matches str AND str2.
     */
    function goToMatched(str, str2) {

        /**
         * Testing:
         * http://localhost:8888/temp/node/test/?desc=files/a-desc.php&json=files/a.json
         * 
         * goToMatched("4.8.3");
         * goToMatched("d27658a9-e2f1-48c7-b898-cff7eaab1586");
         * 
         */

        var $contained = null;
        if(typeof str==="object") { // reg exp is type of object
            $contained = $(`.renderjson .key:regex(${str}),
                            .renderjson .object:regex(${str}),
                            .renderjson .number:regex(${str}),
                            .renderjson .string:regex(${str})`);
            console.log("Running reg exp");
        } else { // string matching
            $contained = $(`.renderjson .key:contains(${str}),
                            .renderjson .object:contains(${str}),
                            .renderjson .number:contains(${str}),
                            .renderjson .string:contains(${str})`);
        }

        // var $contained = $(`.renderjson :regex(${str})`);
        console.log("Matched: ", $contained.length);
        if($contained.length===0) return;
        var disableScrolling = false;

        for(var i=0; i<$contained.length; i++) {

            var toExpands = [];

            // Compound AND
            if(typeof str2!=="undefined") {
                if(typeof str2==="object") { // reg exp is type of object
                    console.log("Running reg exp on second parameter");
                    if( $contained.eq(i).parent().find(`:regex("${str2}")`).length===0 )
                        continue;
                } else { // string matching
                    if( $contained.eq(i).parent().find(`:contains("${str2}")`).length===0 )
                        continue;
                }
            }
            
            var $body = $contained.eq(i).parent().closest(".gotoable"),
                $header = $body.prev();
            
            if($body.length===0) return;
            toExpands.push({$body,$header});

            var limit = 99;

            while(true) {
                $body = $body.parent().closest(".gotoable");

                // debugger;
                
                if($body.length===0) break;

                $header = $body.prev();
                toExpands.push({$body,$header});
                //console.log($body);

                if(limit<0) break;
            }
            toExpands.reverse();
            toExpands.forEach( (toExpand,i)=> { 
                $header = toExpand.$header;
                $body = toExpand.$body;

                $header.css("display", "none");
                $body.css("display", "inline");

                if(i===toExpands.length-1 && !disableScrolling) {

                    // Don't cause nonstop scrolling because too many results
                    disableScrolling = true;
                    setTimeout( ()=>{ disableScrolling=false; }, 1000 );

                    var $el = toExpands[i].$body;
                    $('html,body').animate({
                        scrollTop: $el.offset().top
                    });
                }
            });

            console.log(toExpands);
        } // for

    } // goToMatched
    </script>

</head>
    <body>
        <h4 onclick='window.location.href="index.php"' style="cursor:pointer;">JSON / JS Object / Database Describer</h4>
        <h6 style="margin-bottom:0;">By Weng Fei Fung</h6>
        <span style="display:block; margin-bottom:10px;">Check console or <a href="README.md">README.md</a> for directions.</span>
        <div id="container">
            <div id="json"></div>
            <div id="desc"></div>
        </div>

        
        <!-- Designer: Open Sans, Lato, FontAwesome, Waypoints, Skrollr, Pixel-Em-Converter -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300|Open+Sans+Condensed:300" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.0/jquery.waypoints.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/skrollr/0.6.30/skrollr.min.js"></script>
        <script src="https://raw.githack.com/filamentgroup/jQuery-Pixel-Em-Converter/master/pxem.jQuery.js"></script>
        
        <!-- Rendering: Handlebars JS, LiveQuery, Sprintf JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.js"></script>
        <script src="https://raw.githack.com/hazzik/livequery/master/src/jquery.livequery.js"></script>
        <script src="https://raw.githack.com/azatoth/jquery-sprintf/master/jquery.sprintf.js"></script>
        
        <!-- Compatibility: Modernizr, jQuery Migrate (check browser) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
        <script src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        
        <!-- Mobile: jQuery UI, jQuery UI Touch Punch -->
        <link href="https://code.jquery.com/ui/1.11.3/themes/ui-lightness/jquery-ui.css" rel="stylesheet"/>
        <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
       
        <!-- Bootstrap JS -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        
        <!-- Friendlier API: ListHandlers, Timeout -->
        <script src="https://raw.githack.com/Inducido/jquery-handler-toolkit.js/master/jquery-handler-toolkit.js"></script>
        <script src="https://raw.githack.com/tkem/jquery-timeout/master/src/jquery.timeout.js"></script>

    </body>
</html>