<?php
// Make sure we have a payload, stop if we do not.
if( ! isset( $_POST['payload'] ) )
 die( '<h1>No payload present</h1><p>A GitHub POST payload is required to deploy from this script.</p>' );

/**
 * Tell the script this is an active end point.
 */
define( 'ACTIVE_DEPLOY_ENDPOINT', true );

require_once 'deploy-config.php';

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
new GitHub_Deploy( $_POST['payload'] );
