var PQP_DETAILS = true;
var PQP_HEIGHT = "short";

addEvent(window, 'load', function(){
    document.getElementById("pqp-container").style.display = "block";
});

function changeTab(tab){
    var pQp = document.getElementById('pQp');
    hideAllTabs();
    addClassName(pQp, tab, true);
}

function hideAllTabs(){
    var pQp = document.getElementById('pQp');
    removeClassName(pQp, 'console');
    removeClassName(pQp, 'speed');
    removeClassName(pQp, 'queries');
    removeClassName(pQp, 'memory');
    removeClassName(pQp, 'files');
}

function toggleDetails(){
    var container = document.getElementById('pqp-container');
    
    if (PQP_DETAILS) {
        addClassName(container, 'hideDetails', true);
        PQP_DETAILS = false;
    }
    else {
        removeClassName(container, 'hideDetails');
        PQP_DETAILS = true;
    }
}

function toggleHeight(){
    var container = document.getElementById('pqp-container');
    
    if (PQP_HEIGHT == "short") {
        addClassName(container, 'tallDetails', true);
        PQP_HEIGHT = "tall";
    }
    else {
        removeClassName(container, 'tallDetails');
        PQP_HEIGHT = "short";
    }
}


//http://www.bigbold.com/snippets/posts/show/2630
function addClassName(objElement, strClass, blnMayAlreadyExist){
    if (objElement.className) {
        var arrList = objElement.className.split(' ');
        if (blnMayAlreadyExist) {
            var strClassUpper = strClass.toUpperCase();
            for (var i = 0; i < arrList.length; i++) {
                if (arrList[i].toUpperCase() == strClassUpper) {
                    arrList.splice(i, 1);
                    i--;
                }
            }
        }
        arrList[arrList.length] = strClass;
        objElement.className = arrList.join(' ');
    }
    else {
        objElement.className = strClass;
    }
}

//http://www.bigbold.com/snippets/posts/show/2630
function removeClassName(objElement, strClass){
    if (objElement.className) {
        var arrList = objElement.className.split(' ');
        var strClassUpper = strClass.toUpperCase();
        for (var i = 0; i < arrList.length; i++) {
            if (arrList[i].toUpperCase() == strClassUpper) {
                arrList.splice(i, 1);
                i--;
            }
        }
        objElement.className = arrList.join(' ');
    }
}

//http://ejohn.org/projects/flexible-javascript-events/
function addEvent(obj, type, fn){
    if (obj.attachEvent) {
        obj["e" + type + fn] = fn;
        obj[type + fn] = function(){
            obj["e" + type + fn](window.event)
        };
        obj.attachEvent("on" + type, obj[type + fn]);
    }
    else {
        obj.addEventListener(type, fn, false);
    }
}
