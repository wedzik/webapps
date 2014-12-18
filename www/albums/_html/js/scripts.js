function initDisplay()
{
    getViewport();
    if (index)
    {
        resizeImage();
    }
    document.getElementById('box_image').style.width = viewportWidth;
    document.getElementById('box_image').style.height = viewportHeight;
    document.getElementById('box_wait').style.width = viewportWidth;
    document.getElementById('box_wait').style.height = viewportHeight;
    document.getElementById('box_gallery').style.width = viewportWidth;
    document.getElementById('box_gallery').style.height = viewportHeight;
    document.getElementById('box_info').style.height = viewportHeight-20;
    showMenu();
}

function fullSize()
{
    if (actualSize == true)
    {
        actualSize = false;
        initDisplay();
    }
    else
    {
        actualSize = true;
        initDisplay();
    }
}


function nextImage(direction)
{
    var nextIndex;
    if (!index)
    {
        if (direction > 0)
        {
            return 1;
        }
        else
        {
            return (imgLink.length - 1);
        }
    }
    var nextImg = index + direction;
    if (nextImg > imgLink.length - 1)
    {
        nextImg = 1;
    }
    if (nextImg < 1)
    {
        nextImg = imgLink.length - 1;
    }
    return nextImg;
}


function cycleImg(direction)
{
    if ((imgLink.length>0)&&naviOk)
    {
        openImageView(nextImage(direction), false);
    }
}

function setOpacity(id, opacity)
{
    var element = document.getElementById(id).style;
    element.opacity = (opacity / 100);	// std
    element.MozOpacity = (opacity / 100);	// firefox
    element.filter = 'alpha(opacity=' + opacity + ')';	// IE
    element.KhtmlOpacity = (opacity / 100);	// Mac
}


function fadeOpacity(id, opacityStart, opacityEnd, msToFade)
{
    var element = document.getElementById(id);
    var currentTime = new Date().getTime();
    element.opacityStart = opacityStart;
    element.opacityEnd = opacityEnd;
    element.timeStart = currentTime;
    element.timeEnd = currentTime + msToFade;
    fadeLoop(id, currentTime);
}


function fadeLoop(id, timeStarted)
{
    var element = document.getElementById(id);
    if (timeStarted != element.timeStart)
    {
        return;
    }
    var currentTime = new Date().getTime();
    var frac = (currentTime - element.timeStart) / (element.timeEnd - element.timeStart);
    if (frac >= 1)
    {
        setOpacity(id, element.opacityEnd);
        if (element.opacityEnd == 0)
        {
            element.style.visibility='hidden';
        }
        return;
    }
    setOpacity(id, ((element.opacityEnd - element.opacityStart) * frac) + element.opacityStart);
    setTimeout("fadeLoop('" + id + "', " + timeStarted + ")", 50);
}


function preloadImage(imgId, full) {
    if ((preloaded != imgId) || (preloadedFull != full)) {
        preloadImg = new Image();

        preloadImg.src = '';
        if (full) {
            preloadImg.src = phpSelf + '?cmd=image&sfpg=' + imgLink[imgId];
        } else {
            preloadImg.src = phpSelf + '?cmd=preview&sfpg=' + imgLink[imgId];
        }
        preloadedFull = 1;

        preloaded = imgId;
    }
}

function downloadImage(id){
    url = phpSelf+'?cmd=dl&sfpg='+imgLink[id];
    var link = document.createElement("a");
    link.href = url;
    document.body.appendChild(link);
    link.click();
}