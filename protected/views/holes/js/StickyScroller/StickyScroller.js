/*****************************************************************************/
// Class Scroller
// Purpose: Create a fixed scroller
// Parameters:
//  obj: The object that will be scrolling
//  start: What distance from the top (in px) the effect starts
//  end: What distance from the top (in px) the effect ends
//  interval: What scroll distance triggers the callback
//  range: How many pixels after the
//  margin: Margin from the top of the browser
//  distance: How far the obj will move
// Dependencies:
//  GetSet class. Included in Vert Library @ http://vertstudios.com/vertlib.js
/******************************************************************************/

function StickyScroller(obj, options)
{        
    //Store function scope
    var $this = this;
    
    //Store initial top and left/right values
    var top = $(obj).css('top');
    var left = $(obj).css('left');
    var right = $(obj).css('right');
    
    var scroll = 0;
    var tempScroll = 0;
    
    //------------------------------------------------------------
    // Set default property values
    //------------------------------------------------------------
    var defaults = {
    start: 0,
    end: 1000,
    interval: 400,
    margin: parseInt(top, 10),
    range: 400
    },	settings = jQuery.extend(defaults,options);
    obj = $(obj);
    
    settings.index = 0;
    settings.oldIndex = 0;

    //Accessors for settings
    GetSet.getters({scope: $this, obj: settings});
    
    //------------------------------------------------------------//
    //                      Callback Functions                    //
    //------------------------------------------------------------//
    var Callback = {};
    
    Callback.newIndex = function(){};  //When the index changes
    Callback.limbo = function(){};     //When scroller not in range
    Callback.scroll = function(){};    //On window scroll
    
    //Get setters for Callback functions
    GetSet.setters({scope: this, prefix: "on", obj: Callback});   
    
    //=========================================================//
    //Public distanceFrom
    //Purpose: Determines the distance in pixels between
    //         the scroller and an index
    //Parameters:
    //  index: The index whose distance from scroller will be calculated
    //Postcondition: Returns an integer
    //=========================================================//
    this.distanceFrom = function(index)
    {        
        tempScroll = $(window).scrollTop();
        
        //Check for both references: "Top" of the range and "bottom"
        var top = index*settings.interval;
        var bottom = index*settings.interval + settings.range;
        
        var distanceFromTop = Math.abs(tempScroll-top);
        var distanceFromBottom = Math.abs(tempScroll-bottom);
        
        //Return the smallest distance
        if(distanceFromTop < distanceFromBottom)
        {
            return distanceFromTop;
        }
        else
        {
            return distanceFromBottom;
        }        
    };
    
    //=========================================================//
    //Public closestIndex
    //Purpose: Determines the closest index
    //Postcondition: Returns the closest index as an integer
    //=========================================================//
    this.closestIndex = function()
    {
        //If index is 0, automatically return 1
        if(settings.index === 0)
        {
            return 1;
        }        
        
        //Distance from next/previous index
        var dPrev = this.distanceFrom(settings.index-1);
        var dNext = this.distanceFrom(settings.index+1);
        
        //Return the index associated with the smallest distance
        if(dPrev <= dNext)
        {
            return settings.index-1;
        }
        else
        {
            return settings.index+1;
        }
    };
    
    //=========================================================//
    //Private getIndex
    //Purpose: returns index
    //=========================================================//
    var getIndex = function()
    {        
        tempScroll = $(window).scrollTop() + settings.margin;        
        
        //Make sure movement would be in the bounds
        if(tempScroll > settings.start && tempScroll < settings.end)
        {                                       
            //Possible new index
            tempIndex = Math.floor((tempScroll-settings.start)/settings.interval);
            
            //Make sure the index is different before reassigning
            //or executing the callback
            if(tempIndex !== settings.index)
            {
                //Store old index
                settings.oldIndex = settings.index;                
                
                //Assign new index
                settings.index = tempIndex;                
            }
        }
        //If tempScroll goes beyond end mark, set distance at end mark
        else if(tempScroll >= settings.end)
        {
            settings.oldIndex = settings.index;
            settings.index = Math.floor((settings.end-settings.start)/settings.interval);
        }
        //If tempScroll goes beyond beginning mark, set distance at start
        else
        {
            settings.oldIndex = settings.index;
            settings.index = 0;
        }        
    };
    
    //=========================================================//
    //Public firstIndex
    //Purpose: Returns first index
    //Postcondition: Returns an integer
    //=========================================================//
    this.firstIndex = function()
    {
        return 0;
    };
    
    //=========================================================//
    //Public lastIndex
    //Purpose: Returns last index
    //Postcondition: Returns an integer
    //=========================================================//
    this.lastIndex = function()
    {
        return Math.floor((settings.end-settings.start)/settings.interval);
    };
    
    //=========================================================//
    //Public inRange
    //Purpose: Determines if the scroller is in interval range
    //Postcondition: Returns boolean
    //=========================================================//
    this.inRange = function()
    {      
        var scroll = $(window).scrollTop() - settings.start + settings.margin;        
        
        var inRange = (scroll >= settings.index * settings.interval) &&
        (scroll <= (settings.index*settings.interval + settings.range));

        return inRange;
    };
    
    
    //------------------------------------------------------------//
    //                    On Browser Scroll                       //
    //------------------------------------------------------------//    
    var wrap = $('<div id="scrollcontainer">').css(
    {
        width: obj.width(),
        height: obj.height(),
        position: "absolute"
    });

    obj.wrap(wrap);
    
    $(window).scroll(function()
    {
        scroll = $(window).scrollTop() + settings.margin;
        
        //Get the current index
        getIndex();
                            
        //If scroll less than beginning, set back to beginning
        if(scroll < settings.start)
        {
           $(obj).css({
            position : 'absolute',
            top: 0,
            left: 0,
            right: 0});
           
           $("#scrollcontainer").css({
            position : 'absolute',
            top: settings.start,
            left: left,
            right: right});
        }
        
        //If scroll greater than ending position, set to end
        else if(scroll > settings.end) 
        {
           $(obj).css({
            position : 'absolute',
            top: 0,
            left: 0,
            right: 0});
           
           $("#scrollcontainer").css({
            position : 'absolute',
            top: settings.end,
            left: left,
            right: right});
           
        }
        
        //Make sure we stay in the specified boundaries
        else
        {
            //Put back to fixed
            $(obj).css({
            position : 'fixed',
            top: settings.margin,
            left: left,
            right: right});
        }        
                                    
        //If in the specified range and a new index, do the callback        
        if(settings.oldIndex !== settings.index)
        {
           Callback.newIndex(settings.index);
        }
        
        //Do the "limbo" call back, which is a callback that executes when
        //the scroller is not in the range, but still between start and end
        if( !$this.inRange() && scroll > settings.start && scroll < settings.end )
        {
           Callback.limbo(settings.index);
        }
        
        //Do the scroll callback regardless of what happens
        Callback.scroll(settings.index);
    });
}