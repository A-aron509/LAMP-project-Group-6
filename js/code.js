const urlBase = 'https://lampsxyz.online/LAMPAPI';
const extension = 'php';
let xhr = new XMLHttpRequest();

let userId = 0;
let FirstName = "";
let LastName = "";


function goHome()
{
	window.location.href = "index.html";
	}

function doLogin()
{
	
	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;
	var hash = md5(password);
		document.getElementById("loginResult").innerHTML = "";


	let tmp = {Login:login,Password:hash};

	let jsonPayload = JSON.stringify(tmp);
	
 //alert (jsonPayload);
 
	let url = urlBase + '/Login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
 
 
 
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

  try {
    xhr.onreadystatechange = function()  {
    
			if (this.readyState == 4 && this.status == 200){
        let jsonObject= JSON.parse(xhr.responseText); //should be parse
		userId= jsonObject.id;
      
      if (userId<1){
					document.getElementById("loginResult").innerHTML = "User/Password combination incorrect"; //gets contents by HTML file
                                                           
					return;
                 }
                
                	FirstName = jsonObject.FirstName; //toggle case due to database fields
	                LastName = jsonObject.LastName;
                    saveCookie(); // 	firstname +   lastname
                    window.location.href="color.html"; //when user does sucessful login, take to another window
      }
    };
	xhr.send(jsonPayload);
		
	}
	catch(err)
	{
		document.getElementById("loginResult").innerHTML = err.message;
	}

}



function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime() + (minutes * 60 * 1000));
	let expires = ";expires=" + date.toGMTString() + ";path=/";
	document.cookie = "firstName=" + FirstName + expires;
	document.cookie = "lastName=" + LastName + expires;
	document.cookie = "userId=" + userId + expires;
}


function readCookie()
{
	userId = -1;
	let data = document.cookie;
	let splits = data.split(";");
	for (var i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if (tokens[0] == "firstName")
		{
			FirstName = tokens[1];
		}
		else if (tokens[0] == "lastName")
		{
			LastName = tokens[1];
		}
		else if (tokens[0] == "userId")
		{
			userId = parseInt(tokens[1].trim());
		}
	}
	if (userId < 0)
	{
		window.location.href = "index.html";
	}
	else
	{
				   let userNameElem = document.getElementById("userName");
		   if (userNameElem) {
			   userNameElem.innerHTML = "Logged in as " + FirstName + " " + LastName;
		   }
		
	}
}


function doLogout()
{
	userId = 0;
	FirstName = "";
	LastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}



function addColor() { 
	let newColor = document.getElementById("colorText").value;
	document.getElementById("colorAddResult").innerHTML = "";

	let tmp = {Color:newColor,UserId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/AddColor.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("colorAddResult").innerHTML = "Color has been added";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("colorAddResult").innerHTML = err.message;
	}
	}

function searchColor() {

	let srch = document.getElementById("searchText").value;
	document.getElementById("colorSearchResult").innerHTML = "";
	
	let colorList = "";

	let tmp = {search:srch,userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/SearchColors.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				document.getElementById("colorSearchResult").innerHTML = "Color(s) has been retrieved";
				let jsonObject = JSON.parse( xhr.responseText );
					


				for( let i=0; i<jsonObject.results.length; i++ ) //gives array
				{
					colorList += jsonObject.results[i];
					if( i < jsonObject.results.length - 1 )
					{
						colorList += "<br />\r\n";
					}
					 {
					//if user has color added to their list, then we show it and add it to the search results



					}
				}
				
				document.getElementsByTagName("p")[0].innerHTML = colorList;
			}
			
		};

			

		xhr.send(jsonPayload);
	
  } catch(err) {
		document.getElementById("colorSearchResult").innerHTML = err.message;
	}
	
} 
//--------------------------------------------------------------------
function searchContact() { 
let srch = document.getElementById("searchText").value;
	document.getElementById("contactSearchResult").innerHTML = "";
	
	let contactList = "";

	let tmp = {search:srch,userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/SearchContact.' + extension;
 
 
	

}



function deleteContact() { // ///contact to del in book


}

function editContact() {  ///contact to edit in book


}



function addContact() { ///contact to book

	let FirstName = document.getElementById("contactAddFirst").value;
	let LastName = document.getElementById("contactAddLast").value;
	let Phone = document.getElementById("contactAddPhone").value;
 	let Email = document.getElementById("contactAddEmail").value;
	document.getElementById("contactAddResult").innerHTML = "";


if (FirstName == "" || LastName == "" || Email == "") {
		document.getElementById("contactAddResult").innerHTML = "Please fill in all fields.";
		return;
	}


	document.getElementById("contactAddResult").innerHTML = "";

	let tmp = {FirstName:FirstName, LastName:LastName, Phone: Phone, Email:Email, userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/contacts/AddContact.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
 
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("contactAddResult").innerHTML = "Contact has been added";
        window.location.href="color.html"; //takes user to login page after successful sign up
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("contactAddResult").innerHTML = err.message;
	}

}


//--------------------------------------------------------------------

function doSignup() { //takes user to sign up page
let url = urlBase + '/Signup.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);

	window.location.href="SignUp.html"; 
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

  

}

function addUser() { //puts user info into database, then takes user to login page
	let FirstName = document.getElementById("FirstName").value;
	let LastName = document.getElementById("LastName").value;
	let User = document.getElementById("User").value;
	let Password = document.getElementById("Password").value;
	document.getElementById("NewAccount").innerHTML = "";

	//if fields are empty, then we show error message and do not add user to database
	if (FirstName == "" || LastName == "" || User == "" || Password == "") {
		document.getElementById("NewAccount").innerHTML = "Please fill in all fields.";
		return;
	}

	var hash = md5(Password);
	let tmp = {FirstName:FirstName, LastName:LastName, Login:User, Password:hash};
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/Signup.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("NewAccount").innerHTML = "User has been added";
				window.location.href="index.html"; //takes user to login page after successful sign up
			}
		};
		xhr.send(jsonPayload);
	} catch(err)
	{
		document.getElementById("NewAccount").innerHTML = err.message;
	}

}