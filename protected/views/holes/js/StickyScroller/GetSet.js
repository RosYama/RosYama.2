/************************************************************/
//Class GetSet
//Purpose: Creates dynamic getters and setters
/************************************************************/

var GetSet = {};

//=========================================================//
//Public Method override
//Purpose: Override default values through iteration
//Parameters:
//  obj: The object whose default values will be overridden
//Postcondition: options Object is altered
//=========================================================//
GetSet.override = function(options, defaults)
{
    //Store this scope
    var $this = options;
    
    
    for (var i in defaults)
    {
        if(!($this[i]))
        {
            $this[i] = defaults[i];
        }        
    }
};

//=========================================================//
//Public getFunctions
//Purpose: Copies one objects functions to another
//Parameters:
//  template: The object whose method will be copied
//  recepient: The object receiving the template methods
//Postcondition: recepient object is altered
//=========================================================//
GetSet.getFunctions = function(template, recepient)
{    
    for (var i in template)
    {
        if(template[i].constructor == Function)
        {
            recepient[i] = template[i];
        }
    }
};


//=========================================================//
//Public Method gettters
//Purpose: Dynamically creates accessor methods(getters)
//Parameters: 
//  scope: The scope in which the accessor methods will be
//         applied
//  prefix: Goes before the property. i.e. (get)Name
//  camel: whether to induce camel case
//  obj: Accessors
//Postcondition: scope has been altered to include
//accessor methods
//=========================================================//
GetSet.getters = function(options)
{   
    //Over-ride default values
    var defaults =
    {
        prefix: "get",
        camel: true
    };
    
    //Override defaults values
    GetSet.override(options, defaults);
    
    //If prefix is set to 'none', force blank. A blank string as a parameter
    //evaluates to null for some reason.
    options.prefix = (options.prefix === "none") ? "" : options.prefix;
    
    //Iterate through the properties of the object
    var str;
    for ( var i in options.obj )
    {
        //If camel case is enabled and no blank prefix
        if(options.camel && options.prefix != "")
        {
            str = i.charAt(0).toUpperCase() + i.substr(1);
        }
        else
        {
            str = i;
        }
        (function(i)
        {
                // Dynamically create an accessor method
                options.scope[ options.prefix + str ] = function()
                {
                        return options.obj[i];
                };  
            })(i);
    }
};

//=========================================================//
//Public Method setters
//Purpose: Dynamically creates muator methods(setters)
//Parameters: 
//  scope: The scope in which the mutator methods will be
//         applied
//  prefix: Goes before the property. i.e. (set)Name
//  camel: whether to induce camel case
//  obj: The object that will have mutators
//Postcondition: scope has been altered to include mutator
//methods
//=========================================================//
GetSet.setters = function(options)
{
    //Over-ride default values
    var defaults =
    {
        prefix: "set",
        camel: true
    };
    
    //Override defaults values
    GetSet.override(options, defaults);
    
    //If prefix is set to 'none', force blank. A blank string as a parameter
    //evaluates to null for some reason.
    options.prefix = (options.prefix === "none") ? "" : options.prefix;    
    
    //Iterate through the properties of the object
    var str;
    for ( var i in options.obj )
    {
        //If camel case is enabled and no blank prefix
        if(options.camel && options.prefix != "")
        {
            str = i.charAt(0).toUpperCase() + i.substr(1);
        }
        else
        {
            str = i;
        }
        (function(i)
        {
                // Dynamically create an accessor method
                options.scope[ options.prefix + str ] = function(val)
                {
                       options.obj[i] = val;
                };  
            })(i);
    }
};