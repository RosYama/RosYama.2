<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">  
<head>
    <title>Sticky Scroller</title>
    <link rel="stylesheet" type="text/css" href="demo.css" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>
    <script type="text/javascript" src="http://www.vertstudios.com/vertlib.min.js"></script>
    <script type="text/javascript" src="StickyScroller.min.js"></script>
    <script type="text/javascript" src="GetSet.js"></script>
    
        
    <script type="text/javascript">
    $(window).load(function()
    {
        var scroller = new StickyScroller("#scrollbox",
        {
            start: 400,
            end: 1900,
            interval: 300,
            range: 100,
            margin: 100
        });
                
        var opacity = .25;
        var fadeTime = 300;
        var current;
                
        //Apply opacity to all but first item.
        $(".block").slice(1).css('opacity', opacity);
        
        
        scroller.onNewIndex(function(index)
        {
            $("#scrollbox").html("Index " + index);
        });
        
        scroller.onScroll(function(index)
        {                        
            if(scroller.inRange())
            {
                $(".block").eq(index).stop(true).animate({"opacity": 1}, fadeTime);
                if(!(
                (index === 0 || index === scroller.lastIndex()) &&
                (scroller.getOldIndex() === 0 || scroller.getOldIndex() === scroller.lastIndex())
                    ))
                {
                    $(".block").eq(scroller.getOldIndex()).stop(true).animate({"opacity": opacity}, fadeTime);
                }
            }
        });
        

    });

    </script>
    
</head>
<body>
    <div id="main">
        <div id="scrollbox">Index 0</div>
        <div id="blocks">
        <div class="block"></div>
        <div class="block"></div>
        <div class="block"></div>
        <div class="block"></div>
        <div class="block"></div>
        <div class="block"></div>
        <div class="block"></div>
        <div id="blankspace"></div>
        </div>
    </div>
    
</body>
</html>