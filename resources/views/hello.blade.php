<h1>user Login</h1>
<form action="users" method = "POST"> 
@crsf
<input type="text" name = "email" placeholder = "enter email"/> <br>
<span style = "color:red"> @errors("email"){{$message}}@enderror</span> <br>
<input type="password" name = "userpassword" placeholder = "enter user password"/> <br>
<span style = "color:red"> @errors("password"){{$message}}@enderror</span> <br>
<button type = "submit" >Login</button>
</form>