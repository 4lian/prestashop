// JavaScript Document
 
isIE=document.all;
isNN=!document.all&&document.getElementById;
isHot=false;
var MovableItem
var maxTiles=100
var moveObject=new Array(maxTiles)
var ActiveSwatch
//------------------
// functions
//------------------
 
 
function startMove(e){
    if (!MovableItem){return;}
    canvas=isIE ? "BODY" : "HTML";
    	activeItem=isIE ? event.srcElement : e.target;  
    	offsetx=isIE ? event.clientX : e.clientX;
    	offsety=isIE ? event.clientY : e.clientY;
    	lastX=parseInt(MovableItem.style.left);
    	lastY=parseInt(MovableItem.style.top);
    	lastW=parseInt(MovableItem.style.width);
    	lastH=parseInt(MovableItem.style.height);
	if (offsetx+scrollAmt[0]>=(MovableItem.parentNode.offsetLeft+parseInt(MovableItem.style.left)+(MovableItem.offsetWidth*.98))|| offsety+scrollAmt[1]>=(MovableItem.parentNode.offsetTop+parseInt(MovableItem.style.top)+(MovableItem.offsetHeight*.98)) ){edge=true; MovableItem.style.cursor="se-resize"} else{edge=false;MovableItem.style.cursor="move"}
	moveEnabled=true;
    document.onmousemove=moveIt;
}
 
function moveIt(e){
  if (!moveEnabled||!MovableItem) return;
	// display info during testing
	MovableItem.innerHTML='<span style="position:absolute; left:0px; top:0px;">&nbsp;'+MovableItem.offsetLeft+', '+MovableItem.offsetTop+' <br>&nbsp;'+
		MovableItem.offsetWidth+', '+MovableItem.offsetHeight+' <br>'+
		'&nbsp;<b>'+(parseInt(MovableItem.id.replace('popup',''))+1)+'</b>'
		//moveObject[0].style.zIndex+' '+moveObject[1].style.zIndex+' '+moveObject[2].style.zIndex+' '+moveObject[3].style.zIndex+'</span>'
 	if (edge){
  		MovableItem.style.width=isIE ? event.clientX-offsetx +lastW : e.clientX-offsetx+lastW; 
  		MovableItem.style.height=isIE ? event.clientY-offsety +lastH : e.clientY-offsety+lastH; 
		return false;
  	} else{
  		MovableItem.style.left=isIE ? lastX+event.clientX-offsetx : lastX+e.clientX-offsetx; 
  		MovableItem.style.top=isIE ? lastY+event.clientY-offsety : lastY+e.clientY-offsety;
  		return false;
	}  
}
 
 
function poplayer(topObject){
  if (!topObject) {return;}
	for (var i=0; i<moveObject.length; i++){
		moveObject[i].style.borderColor='silver'
		if (moveObject[i].style.zIndex>topObject.style.zIndex-1 && moveObject[i]!=topObject)
		{moveObject[i].style.zIndex=moveObject[i].style.zIndex-1}
	}
   	topObject.style.zIndex=moveObject.length-1
	topObject.style.borderColor='black'
	ActiveSwatch=topObject
	showLink(parseInt(ActiveSwatch.id.replace('popup','')))
 
}
 
function test(obj, X, Y){
	scrollAmt=puGetScrollXY(); //0 is X and 1 is Y
	//alert(X+scrollAmt[0]+' '+(obj.parentNode.offsetLeft+parseInt(obj.style.left)+(obj.offsetWidth*.98)) )
	//alert(obj.style.left+' '+obj.offsetWidth+' '+X+'  \n'+obj.style.top+' '+obj.offsetHeight+' '+Y+' '+obj.parentNode.offsetTop+' '+scrollAmt[1])
	if (X+scrollAmt[0]>=(obj.parentNode.offsetLeft+parseInt(obj.style.left)+(obj.offsetWidth*.98)) || Y+scrollAmt[1]>=(obj.parentNode.offsetTop+parseInt(obj.style.top)+(obj.offsetHeight*.98)) ) {obj.style.cursor="se-resize"} else{obj.style.cursor="move"}
}
 
function createTile(){
	// when user clicks the deck, deal out a new tile
	for (var i=0; i<maxTiles; i++){
		if (moveObject[i].style.visibility=="hidden"){
			moveObject[i].style.width=100
			moveObject[i].style.height=80
			moveObject[i].style.left=-10
			moveObject[i].style.top=-10
			moveObject[i].style.visibility="visible"
			moveObject[i].style.display="block"
			poplayer(moveObject[i])
			showLink(i)
		return
		}
		
	}
}
 
function showLink(itm){
	for (var i=0; i<maxTiles; i++){
	document.getElementById('linker'+i).style.visibility='hidden'
	document.getElementById('title'+i).style.visibility='hidden'
	document.getElementById('linker'+i).style.display='none'
	document.getElementById('title'+i).style.display='none'
	}
	document.getElementById('linker'+itm).style.visibility='visible'
	document.getElementById('title'+itm).style.visibility='visible'
	document.getElementById('linker'+itm).style.display='block'
	document.getElementById('title'+itm).style.display='block'
}
 
function killTile(){
	if (!ActiveSwatch){return}
	ActiveSwatch.style.visibility="hidden"
	ActiveSwatch.style.display="none"
	document.getElementById('linker'+parseInt(ActiveSwatch.id.replace('popup',''))).value=''
	document.getElementById('title'+parseInt(ActiveSwatch.id.replace('popup',''))).value=''
	ActiveSwatch=null
}
 
 
function makeWorkspace(){
	linklist=""
	workspace='<img style="position:relative" id=mapimage galleryimg=false src="http://www.isdntek.com/tagbot/misc/bambi.jpg">'
 
	for (var i=0; i<maxTiles; i++){
		linklist=linklist+'<input class=links id=linker'+i+' type=text value="" >'
		linklist=linklist+'<input class=alts id=title'+i+' type=text value="" >'
		workspace=workspace+'<div class=tile id=popup'+i+' style="left:'+(i*10)+'px; top:'+(i*10)+'px; '+
		' width:100px; height:80px;  z-Index:'+i+'; visibility:hidden;" '+
		' title="'+(i+1)+'. drag to move or resize" onSelectStart="return false" '+
		' onmousedown="MovableItem=this; poplayer(this); return false" '+
		' onMouseover="isHot=true;  " '+
		' onMousemove="test(this, event.clientX, event.clientY ) " '+
		' onMouseout ="isHot=false"'+
		' >&nbsp;hotspot '+(i+1)+'&nbsp;</div>'
	}
 
	document.getElementById('linkbox').innerHTML=linklist
	document.getElementById('workSpace2').innerHTML=workspace
 
 
}
 
function loadImage(){
	document.getElementById('mapimage').src=document.getElementById('imageInput').value
	a=setTimeout("resizeImage()",500)
	
}
function resizeImage(){
	document.getElementById('mapimage').parentNode.style.width=document.getElementById('mapimage').offsetWidth
	document.getElementById('mapimage').parentNode.style.height=document.getElementById('mapimage').offsetHeight
}
 
 
 
function makeMap(){
	var key=Math.round(Math.random()*100)
	img=document.getElementById('mapimage')
	// removed the height as it made image disappear
	mapSource='<img src="'+img.src+'" width="'+img.offsetWidth+'" border="0" usemap="#imap_'+key+'" >'
	mapSource=mapSource+'\n<map id="imap_'+key+'" name="imap_'+key+'" >\n'
	var validMap=false
	for (var i=0; i<maxTiles; i++){
		if (moveObject[i].style.visibility=='visible'){
			mapSource=mapSource+
			'  <area shape="rect" '+ 
			'coords="'+moveObject[i].offsetLeft+','+moveObject[i].offsetTop+','+(moveObject[i].offsetLeft+moveObject[i].offsetWidth)+','+(moveObject[i].offsetTop+moveObject[i].offsetHeight)+'" '+
			'alt="'+document.getElementById('title'+i).value+'" '+
			'title="'+document.getElementById('title'+i).value+'" '+
			'href="'+document.getElementById('linker'+i).value+'">\n'
		validMap=true
		}
	}
	mapSource=mapSource+'</map>\n'
	if (!validMap){alert("Create links for your image first")}else {
	document.getElementById('codeWindow').value=mapSource
	}
}
 
 
function puGetScreenSize() {
	var myWidth = 0, myHeight = 0;
	if( typeof( window.innerWidth ) == 'number' ) {
		/*Non-IE*/
		myWidth = window.innerWidth;
		myHeight = window.innerHeight;
	} else if( document.documentElement &&
		( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		/*IE 6+ in 'standards compliant mode'*/
 		myWidth = document.documentElement.clientWidth;
		myHeight = document.documentElement.clientHeight;
	} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		/*IE 4 compatible*/
		myWidth = document.body.clientWidth;
		myHeight = document.body.clientHeight;
	}
	return [ myWidth, myHeight ];
}
 
function puGetScrollXY() {
	var scrollXamt = 0, scrollYamt = 0;
	if( typeof( window.pageYOffset ) == 'number' ) {
		/*Netscape compliant*/
		scrollYamt = window.pageYOffset;
		scrollXamt = window.pageXOffset;
	} else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
		/*DOM compliant*/
		scrollYamt = document.body.scrollTop;
		scrollXamt = document.body.scrollLeft;
	} else if( document.documentElement &&
		( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
		/*IE6 standards compliant mode*/

		scrollYamt = document.documentElement.scrollTop;
		scrollXamt = document.documentElement.scrollLeft;
	}
	return [ scrollXamt, scrollYamt ];
}
 
 
function Preview(displaycode) {
  	  var ww = window.open("","popupwindow","width=600,height=350,directories=no,menubar=yes,status=yes,toolbar=yes,resizable=yes,scrollbars=yes,screenY=0,top=0,screenX=80,left=80" );
    	  ww.document.writeln("<html><head><title>Preview</title></head><body>" );
  	  ww.document.writeln(displaycode);
  	  ww.document.writeln("<br clear=both><hr><form><center><input type=\"submit\" value=\"Close Window\" onClick=\"window.close();return false; \"></center></form>" );
  	  ww.document.writeln("</body></html>" );
  	  ww.document.close() ;
  	  if(document.focus){ ww.document.focus(true)}
}
//------------------
// movable objects
//------------------
 
 
 
//------------------
// initialize
//------------------
function init(){
	document.onmousedown=startMove;
	document.onmouseup=Function("moveEnabled=false; MovableItem=''");
	makeWorkspace();
	for (var i=0; i<maxTiles; i++){ moveObject[i]=document.getElementById('popup'+i)}
	ActiveSwatch=moveObject[0]
}
 