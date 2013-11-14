#vcbot
Just a php script to help with version control busywork.

##Usage
###makeTicket
	# vcbot makeTicket <repo> <ticket>
	# Make a ticket branch from trunk
	vcbot makeTicket frc ticket_1234
*aliases: mt, maketicket, createbranch, cb*
###makeRelease
	# vcbot makeRelease <repo> <release>
	# Make a release branch from trunk
	vcbot makeRelease frc Sep1313
*aliases: mr, makerelease, createrelease, cr*
###deleteTicket
	# vcbot deleteTicket <repo> <ticket>
	# Delete a ticket branch
	vcbot deleteTicket frc ticket_1234
*aliases: dt, deleteticket, deletebranch, db*
###deleteRelease
	# vcbot deleteRelease <repo> <release>
	# Delete a release branch
	vcbot deleteRelease frc Sep1313
*aliases: dr, deleterelease*
###mergeUp
	# vcbot mergeUp <ticket> <release> [<repo>]
	# Merge ticket branch into release branch (for all registered workspaces)
	vcbot mergeUp ticket_1234 Sep1316

	# Merge ticket branch into release branch for one specific workspace
	vcbot mergeUp ticket_1234 Sep1316 frc
*aliases: mergeup, mup*
###rebase
	# vcbot rebase <release> <repo>
	# Make a new release branch from trunk and remerge all ticket branches
	# Will prompt for confirmation for each ticket before trying merge
	vcbot rebase Nov1813 frms

##Installation
* You'll need php cli...
* Clone this project to where you want it to live (we'll call it [VCBOT_DIR])
* Copy [VCBOT_DIR]/vcbot into ~/bin
* Change line 3 of ~/bin/vcbot to php [VCBOT_DIR]/vcbot.php $1 $2 $3 $4
* Personalize [VCBOT_DIR]/config.json to match your workspace setup. For example, the following config assumes that you have 3 workspaces checkedout that you's like to work with. One that known as 'frc', checked out to '~/frc_push' and pointed at a remote svn repo located at 'http://svn.frlabs.com/svn/frc' etc. Optionally a special path to a ticket directory or releases directory can be provided.


```javascript
	{
	    "workspaces": [
	        {
	            "name": "frc",
	            "directoryPath": "~/frc_push",
	            "url": "http://svn.frlabs.com/svn/frc"
	        },
	        {
	            "name": "frms",
	            "directoryPath": "~/frms_push",
	            "url": "http://svn.frlabs.com/svn/frms"
	        },
	        {
	            "name": "chc",
	            "directoryPath": "~/chc_push",
	            "url": "http://svn.frlabs.com/svn/frms/chc",
	            "ticketBase": "branches/core"
	        }
	    ]
	}
```

##Todo
- [x] svn interface implementation
- [ ] git interface implementation
- [x] create ticket
- [x] create release
- [x] merge up
- [x] delete ticket
- [x] delete release
- [ ] rebase ticket
- [x] rebase release
- [ ] print github/trac links
- [ ] tag release