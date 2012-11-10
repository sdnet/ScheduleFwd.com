<div id="loggedInAs" />
	<span style="border-bottom: 1px dashed #CCC; padding: 6px;">
    	Logged in as: <a href="/profile" title="Edit your profile"><?=$user?></a>
        
        &nbsp; | &nbsp;  
        <a href="/logout" title="Logout">Logout</a>
	</span>
</div> <br />

<?
if ($role == "Admin") {
?>
    <div id="adminLeftLinks" style="text-align: center; float: left; width: 98%; margin: 0 auto;">
    	<a href="/home" title="View Your Dashboard">Dashboard</a> |
        <a href="/userMgmt" title="View, Edit and Delete Providers">Providers</a> | 
        <a href="/YourCalendar" title="Generate and View Schedules">Schedules</a> | 
        <a href="/shifts" title="Create and Place Scheduled Shifts">Shifts</a> | 
        <a href="/trades" title="Traded Shifts">Traded Shifts</a> |
        <a href="/messages" title="View Your Messages">Messages</a> | 
        <a href="/groupsAndRoles" title="Create and Edit User Roles and Groups">Groups and Roles</a> | 
        <a href="/rules" title="View and Configure System Rules">Configurations</a>
    </div>
    
	<script type="text/javascript" src="//assets.zendesk.com/external/zenbox/v2.5/zenbox.js"></script>
    <style type="text/css" media="screen, projection">
      @import url(//assets.zendesk.com/external/zenbox/v2.5/zenbox.css);
    </style>
    <script type="text/javascript">
      if (typeof(Zenbox) !== "undefined") {
        Zenbox.init({
          dropboxID:   "20099443",
          url:         "https://forwardintel.zendesk.com",
          tabID:       "Support",
          tabColor:    "black",
          tabPosition: "Right"
        });
      }
    </script>
    
<?
} elseif ($role == "User") {
?>
    <div id="adminLeftLinksUser" style="float: left;">
    	<a href="/home" title="View Your Dashboard">Dashboard</a> |
        <a href="/YourCalendar" title="View Your Schedule">View Your Schedule</a> | 
        <a href="/messages" title="Check Your Messages">Check Your Messages</a> |
        <a href="/trades" title="Trade Requests">Trade Requests</a> |
        <a href="/userDir" title="User Directory">User Directory</a>
    </div>
<? } else { ?>

    <div id="adminLeftLinksUser" style="float: left;">
    	<a href="/home" title="View Your Dashboard">Dashboard</a>
    </div>

<? } ?>