<html>
  <head>
    <title>Login</title>    
  </head>
  <body>
    Please wait ...
    <form method="post" action="{{ acsUrl }}">
      <input type="hidden" name="SAMLResponse" value="{{ SAMLResponse }}" />
    <input type="hidden" name="RelayState" value="{{ RelayState }}" />
      <noscript>
      <div><input type="submit" value="Redirect now!"></div>
      </noscript>
    </form>    
    <script>document.forms[0].submit();</script>
  </body>
</html>