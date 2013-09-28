<?php
// Make sure we have a payload, stop if we do not.
// if( ! isset( $_POST['payload'] ) )
//  die( '<h1>No payload present</h1><p>A GitHub POST payload is required to deploy from this script.</p>' );

/**
 * Tell the script this is an active end point.
 */
define( 'ACTIVE_DEPLOY_ENDPOINT', true );

require_once 'deploy-config.php';

$payload = '{
   "commits":[
    {
     "added":[

     ],
     "author":{
      "email":"lolwut@noway.biz",
      "name":"Garen Torikian",
      "username":"octokitty"
     },
     "committer":{
      "email":"lolwut@noway.biz",
      "name":"Garen Torikian",
      "username":"octokitty"
     },
     "distinct":true,
     "id":"c441029cf673f84c8b7db52d0a5944ee5c52ff89",
     "message":"Test",
     "modified":[
      "README.md"
     ],
     "removed":[

     ],
     "timestamp":"2013-02-22T13:50:07-08:00",
     "url":"https://github.com/octokitty/testing/commit/c441029cf673f84c8b7db52d0a5944ee5c52ff89"
    },
    {
     "added":[

     ],
     "author":{
      "email":"lolwut@noway.biz",
      "name":"Garen Torikian",
      "username":"octokitty"
     },
     "committer":{
      "email":"lolwut@noway.biz",
      "name":"Garen Torikian",
      "username":"octokitty"
     },
     "distinct":true,
     "id":"36c5f2243ed24de58284a96f2a643bed8c028658",
     "message":"This is me testing the windows client.",
     "modified":[
      "README.md"
     ],
     "removed":[

     ],
     "timestamp":"2013-02-22T14:07:13-08:00",
     "url":"https://github.com/octokitty/testing/commit/36c5f2243ed24de58284a96f2a643bed8c028658"
    },
    {
     "added":[
      "words/madame-bovary.txt"
     ],
     "author":{
      "email":"lolwut@noway.biz",
      "name":"Garen Torikian",
      "username":"octokitty"
     },
     "committer":{
      "email":"lolwut@noway.biz",
      "name":"Garen Torikian",
      "username":"octokitty"
     },
     "distinct":true,
     "id":"1481a2de7b2a7d02428ad93446ab166be7793fbb",
     "message":"Rename madame-bovary.txt to words/madame-bovary.txt",
     "modified":[

     ],
     "removed":[
      "madame-bovary.txt"
     ],
     "timestamp":"2013-03-12T08:14:29-07:00",
     "url":"https://github.com/octokitty/testing/commit/1481a2de7b2a7d02428ad93446ab166be7793fbb"
    }
   ],
   "ref":"refs/heads/dzv",
   "repository":{
    "name":"hopenet-main",
    "owner":{
     "email":"lolwut@noway.biz",
     "name":"octokitty"
    },
    "url":"https://github.com/dzerovibe/git-deploy"
   }
}';

/**
 * Deploys GitHub git repos
 */
class GitHub_Deploy extends Deploy {
  /**
   * Decodes and validates the data from github and calls the
   * doploy contructor to deoploy the new code.
   *
   * @param   string  $payload  The JSON encoded payload data.
   */
  function __construct( $payload ) {
    $payload = json_decode( $payload, true );

    $repo_name = $payload['repository']['name'];
    $branch = basename( $payload['ref'] );

    $this->log( '===== START =====' );
    $this->log( 'Received a GitHub payload for "' . $payload['repository']['url'] . '"' );
    $this->log( 'Payload: ' . print_r( $payload, true ), 'DEBUG' );

    // Check if the received repo is in any of our configured deployments
    foreach ( parent::$deployments as $name => $deployment ) {
      if ( $repo_name != $deployment['repo'] ) {
        // Unset any deployments that don't match
        unset( parent::$deployments[$name] );
      }
    }

    // Check all branches updated by this payload and match against our remaining deployments
    $to_process = array();
    foreach ( parent::$deployments as $name => $deployment ) {
      if ( $deployment['branch'] === $branch ) {
        // Flag this deployment for processing
        $to_process[$name] = $deployment;
      }
    }
    parent::$deployments = $to_process;

    foreach ( parent::$deployments as $name => $deployment ) {
      parent::__construct( $name, $deployment );
    }
  }
}
// Starts the deploy attempt.
echo '<pre>'. "\n";
$_POST['payload'] = $payload;
new GitHub_Deploy( $_POST['payload'] );
echo "\n" . '</pre>';
