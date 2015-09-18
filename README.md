# Natterbox Oncall
### Connect customer phoning in on extended support numbers to appropriate destination. Support desk, on call engineer or message service.


Web site must be on the internet and accessible from the Natterbox servers. index.php is called from an http query component in a Natterbox policy. It should use GET and provide one parameter, "callednumber" which should be the number that the customer has dialed.

The script will use the number called, time and day of week, bank holiday status, etc, to return a status of either "call support" if in working hours, "out of hours" if the customers contract does not cover the time they ring in on, or "call engineer" if they are covered out of hours.
In the latter case, the phone number of the on call engineer will be returned.

The result is returned as an XML record

```
<records>
  <record>
    <CalledNumber>0333150XXXX</CalledNumber>
    <Result>call engineer</Result>
    <OncallNumber>+447890YYYYY</OncallNumber>
  </record>
</records>
```
The admin folder should be password protected and provides a simple web GUI to maintain the list and status of on call engineers
