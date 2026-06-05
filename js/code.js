const urlBase = 'https://lampsxyz.online/LAMPAPI';
const extension = 'php';
let xhr = new XMLHttpRequest();

let userId = 0;
let FirstName = "";
let LastName = "";
let currentContact = null; // Store the currently selected contact for editing
//--------------------------------------------------------------------
function colorsMenu(){
	window.location.href = "color.html";
}
function contactsMenu(){
	window.location.href = "contacts.html";
}

function goHome()
{
	window.location.href = "index.html";
	}
 function goBack()
{
	window.location.href = "menu.html";
	}
//--------------------------------------------------------------------
function doLogin()
{
	
	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;
	var hash = md5(password);
		document.getElementById("loginResult").innerHTML = "";


	let tmp = {Login:login,Password:hash};

	let jsonPayload = JSON.stringify(tmp);
 
	let url = urlBase + '/auth/Login.' + extension;

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
                    window.location.href="menu.html"; //when user does sucessful login, take to another window
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
			   userNameElem.innerHTML = "Welcome " + FirstName + " " + LastName;
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

	let url = urlBase + '/colors/AddColor.' + extension;
	
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
	let srchFirst = document.getElementById("ContactFirstSearch").value;
 	let srchLast = document.getElementById("ContactLastSearch").value;
	document.getElementById("contactSearchResult").innerHTML = "";
	document.getElementById("contactSearchResult").style.display = "none";
	
	let contactList = "";

	let tmp = {SearchF:srchFirst,SearchL:srchLast,userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/contacts/SearchContacts.' + extension;
 
 
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() {
			if (this.readyState == 4) {
				if (this.status == 200) {
					let jsonObject = JSON.parse(xhr.responseText);
					
					if (jsonObject.error && jsonObject.error !== "") {
						document.getElementById("contactSearchResult").innerHTML = jsonObject.error;
    	    
						document.getElementById("ContactList").innerHTML = "";
						document.getElementById("contactActions").style.display = "none";
						currentContact = null;
					} else {
						//document.getElementById("contactSearchResult").innerHTML = "Contact has been retrieved";
    	      let jsonObject = JSON.parse( xhr.responseText );                                     
   	
						for (let i = 0; i < jsonObject.results.length; i++) {
							let contact = jsonObject.results[i];
							contactList += "<h3>" + contact.FirstName + " " + contact.LastName + "</h3>";
							contactList += "<p>Phone: " + contact.Phone + "</p>";
							contactList += "<p>Email: " + contact.Email + "</p>";
							if (i < jsonObject.results.length - 1) {
								contactList += "<hr />";
							}
						}
						document.getElementById("ContactList").innerHTML = contactList;
						// Store the first contact for editing
						if (jsonObject.results.length >= 1) {
							currentContact = jsonObject.results[0];
							// Pre-populate edit form with current contact info
							document.getElementById("contactAddFirst").value = currentContact.FirstName;
							document.getElementById("contactAddLast").value = currentContact.LastName;
							document.getElementById("contactAddPhone").value = currentContact.Phone;
							document.getElementById("contactAddEmail").value = currentContact.Email;
						}
						document.getElementById("ContactBook").style.display = "block";
						return true;
					}
				} else {
					document.getElementById("contactSearchResult").innerHTML = "Server error: " + this.status;
					document.getElementById("contactActions").style.display = "none";
					return false;
				}
			}
		};

		xhr.send(jsonPayload);
	}
	catch(err) {
		document.getElementById("contactSearchResult").innerHTML = err.message;
		document.getElementById("contactActions").style.display = "none";
	}
}


function deleteContact() { // ///contact to del in book
	// Determine which contact to delete: prefer the selected contact, otherwise use form inputs
	let first = currentContact ? currentContact.FirstName : document.getElementById("contactAddFirst").value;
	let last = currentContact ? currentContact.LastName : document.getElementById("contactAddLast").value;
	if (!first || !last) {
		document.getElementById("contactDelResult").innerHTML = "Contact not found. Please search for a valid contact first or fill in the first and last name.";
		return;
	}

	document.getElementById("contactDelResult").innerHTML = "";

	let tmp = {FirstName: first, LastName: last, userId: userId};
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/contacts/DeleteContact.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				let jsonObject = JSON.parse(xhr.responseText);
				if (jsonObject.error && jsonObject.error !== "") {
					document.getElementById("contactDelResult").innerHTML = jsonObject.error;
				} else {
					document.getElementById("contactDelResult").innerHTML = "Contact deleted";
					// clear UI
					currentContact = null;
					document.getElementById("contactAddFirst").value = "";
					document.getElementById("contactAddLast").value = "";
					document.getElementById("contactAddPhone").value = "";
					document.getElementById("contactAddEmail").value = "";
					document.getElementById("ContactList").innerHTML = "";
					document.getElementById("contactActions").style.display = "none";
				}
			}
		};
		xhr.send(jsonPayload);
	} catch(err) {
		document.getElementById("contactDelResult").innerHTML = err.message;
	}

}


function editContact() {
	if (!currentContact) {
		document.getElementById("contactAddResult").innerHTML = "Please search for a valid contact first.";
		return;
	}

	let newFirstName = document.getElementById("contactAddFirst").value;
	let newLastName = document.getElementById("contactAddLast").value;
	let Phone = document.getElementById("contactAddPhone").value;
	let Email = document.getElementById("contactAddEmail").value;
	document.getElementById("contactAddResult").innerHTML = "";

	if (newFirstName == "" || newLastName == "" || Phone == "") {
		document.getElementById("contactAddResult").innerHTML = "Please fill in all required fields.";
		return;
	}

	let url = urlBase + '/contacts/EditContact.' + extension;

	
	let tmp = {
		oldFirstName: currentContact.FirstName,
		oldLastName: currentContact.LastName,
		FirstName: newFirstName,
		LastName: newLastName,
		Phone: Phone,
		Email: Email,
		userId: userId
	};
	let jsonPayload = JSON.stringify(tmp);

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

	try {
		xhr.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				let jsonObject = JSON.parse(xhr.responseText);
				
				if (jsonObject.error && jsonObject.error !== "") {
					document.getElementById("contactAddResult").innerHTML = "Error: " + jsonObject.error;
				} else {
					document.getElementById("contactAddResult").innerHTML = "Contact updated successfully";
					// Clear the current contact and form
					currentContact = null;
					document.getElementById("contactAddFirst").value = "";
					document.getElementById("contactAddLast").value = "";
					document.getElementById("contactAddPhone").value = "";
					document.getElementById("contactAddEmail").value = "";
					document.getElementById("ContactList").innerHTML = "";
				}
			}
		};
		xhr.send(jsonPayload);
	} catch(err) {
		document.getElementById("contactAddResult").innerHTML = err.message;
	}
}



function addContact() { ///contact to book

	let FirstName = document.getElementById("contactAddFirst").value;
	let LastName = document.getElementById("contactAddLast").value;
	let Phone = document.getElementById("contactAddPhone").value;
 	let Email = document.getElementById("contactAddEmail").value;
	document.getElementById("contactAddResult").innerHTML = "";


if (FirstName == "" || LastName == "" || Phone == "") {
		document.getElementById("contactAddResult").innerHTML = "Please fill in all required fields.";
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
        //window.location.href="menu.html"; //takes user to login page after successful sign up
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

function doSignup() { // takes user to sign up page
	window.location.href = "SignUp.html";
}

function addUser() { // puts user info into database, then takes user to login page
	let FirstName = document.getElementById("FirstName").value;
	let LastName = document.getElementById("LastName").value;
	let User = document.getElementById("User").value;
	let Password = document.getElementById("Password").value;
	document.getElementById("NewAccount").innerHTML = "";

	// if fields are empty, then we show error message and do not add user to database
	if (FirstName == "" || LastName == "" || User == "" || Password == "") {
		document.getElementById("NewAccount").innerHTML = "Please fill in all required fields.";
		return;
	}

	if (Password.length < 8) {
		document.getElementById("NewAccount").innerHTML = "Password must be at least 8 characters long.";
		return;
	}

	let hash = md5(Password);
	let tmp = {FirstName:FirstName, LastName:LastName, Login:User, Password:hash};
	let jsonPayload = JSON.stringify(tmp);

	let url = urlBase + '/auth/Signup.' + extension;
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

	try {
		xhr.onreadystatechange = function() {
			if (this.readyState == 4) {
				console.log('Signup response status:', this.status);
				console.log('Signup response body:', xhr.responseText);
				if (this.status == 200) {
					try {
						let jsonObject = JSON.parse(xhr.responseText);
						if (jsonObject.error && jsonObject.error !== "") {
							document.getElementById("NewAccount").innerHTML = jsonObject.error;
						} else {
							document.getElementById("NewAccount").innerHTML = "User has been added";
							
						}
					} catch (parseErr) {
						document.getElementById("NewAccount").innerHTML = "Invalid server response: " + xhr.responseText;
					}
				} else {
					document.getElementById("NewAccount").innerHTML = "Server error: " + this.status + " - " + xhr.responseText;
				}
			}
		};
		xhr.onerror = function() {
			document.getElementById("NewAccount").innerHTML = "Network error. Please try again.";
		};
		xhr.send(jsonPayload);
	} catch(err) {
		document.getElementById("NewAccount").innerHTML = err.message;
	}
}
