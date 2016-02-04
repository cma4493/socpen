/**
 * Created by JOSEF FRIEDRICH BALDO on 1/13/2016.
 */
function getProv(str)
{
    document.getElementById("jb_region_code").value = str;
    if (str=="")
    {
        document.getElementById("div_prov").innerHTML="";
        return;
    }
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            document.getElementById("div_prov").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","ajax_prov.php?q="+str,true);
    xmlhttp.send();
}

function getCity(str)
{
    document.getElementById("jb_prov_code").value = str;
    if (str=="")
    {
        document.getElementById("div_city").innerHTML="";
        return;
    }
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            document.getElementById("div_city").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","ajax_city.php?q="+str,true);
    xmlhttp.send();
}

function getBrgy(str)
{
    document.getElementById("jb_city_code").value = str;
    if (str=="")
    {
        document.getElementById("div_brgy").innerHTML="";
        return;
    }
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            document.getElementById("div_brgy").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","ajax_brgy.php?q="+str,true);
    xmlhttp.send();
}