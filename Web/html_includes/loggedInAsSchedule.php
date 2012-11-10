<?
if ($role == "Admin") {
?>
    <div id="adminLeftLinks" style="float: left;">
    	<a href="/home" title="View Your Dashboard">Dashboard</a> |
        <a href="/userMgmt" title="View, Edit and Delete Providers">Providers</a> | 
        <a href="/YourCalendar" title="Generate and View Schedules">Schedules</a> | 
        <a href="/shifts" title="Create and Place Scheduled Shifts">Shifts</a> | 
        <a href="/messages" title="View Your Messages">Messages</a> | 
        <a href="/groupsAndRoles" title="Create and Edit User Roles and Groups">Groups and Roles</a> | 
        <a href="/rules" title="View and Configure System Rules">Configurations</a> |
        <a href="/support" title="Having problems?  Let us know">Get Support</a>
    </div> <br />
<?
} else {
?>
    <div id="adminLeftLinksUser" style="float: left;">
    	<a href="/home" title="View Your Dashboard">Dashboard</a> |
        <a href="/YourCalendar" title="View Your Schedule">View Your Schedule</a> | 
        <a href="/messages" title="Check Your Messages">Check Your Messages</a> |
        <a href="/support" title="Having problems?  Let us know">Get Support</a>
    </div>
<? } ?>

<div id="loggedInAs" />
	<span style="border-bottom: 1px dashed #CCC; padding: 6px;">
    	Logged in as: <a href="/profile" title="Edit your profile"><?=$user?></a>
        
        &nbsp; | &nbsp;  
        <a href="/logout" title="Logout">Logout</a>
	</span> <br />
    
</div>