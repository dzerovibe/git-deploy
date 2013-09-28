<?php
// Make sure we have a payload, stop if we do not.
// if( ! isset( $_POST['payload'] ) )
  // die( '<h1>No payload present</h1><p>A BitBucket POST payload is required to deploy from this script.</p>' );

/**
 * Tell the script this is an active end point.
 */
define( 'ACTIVE_DEPLOY_ENDPOINT', true );

require_once 'deploy-config.php';

$payload = array(
  'repository' => Array
  (
    'website' => NULL,
    'fork' => NULL,
    'name' => 'HopeNet - Main',
    'scm' => 'git',
    'owner' => 'jason_lane',
    'absolute_url' => '/jason_lane/hopenet-main/',
    'slug' => 'hopenet-main',
    'is_private' => '1'
  ),
  'truncated' => NULL,
  'commits' => Array(
    '0' => Array(
      'node' => '4d21b30e9402',
      'files' => Array(),
      'branch' => 'master',
      'utctimestamp' => '2013-09-27 04:43:07+00:00',
      'timestamp' => '2013-09-27 06:43:07',
      'raw_node' => '4d21b30e940277410fcb9c82b4dfc6e4578719fa',
      'message' => 'Merge branch \'develop\'',
      'size' => '-1',
      'author' => 'kakaiba',
      'parents' => Array
      (
        '0' => '53e6539e2c62',
        '1' => 'ba42d5e5421f'
      ),
      'raw_author' => 'Maris Reyes <kakaiba@gmail.com>',
      'revision' => NULL
    ),
    '1' => Array(
      'node' => '4d21b30e9402',
      'files' => Array(),
      'branch' => 'dzv',
      'utctimestamp' => '2013-09-27 04:43:07+00:00',
      'timestamp' => '2013-09-27 06:43:07',
      'raw_node' => '4d21b30e940277410fcb9c82b4dfc6e4578719fa',
      'message' => 'Did something',
      'size' => '-1',
      'author' => 'kakaiba',
      'parents' => Array
      (
        '0' => '53e6539e2c62',
        '1' => 'ba42d5e5421f'
      ),

      'raw_author' => 'Maris Reyes <kakaiba@gmail.com>',
      'revision' => NULL
    ),
    '2' => Array(
      'node' => '4d21b30e9402',
      'files' => Array(),
      'branch' => 'master',
      'utctimestamp' => '2013-09-27 04:43:07+00:00',
      'timestamp' => '2013-09-27 06:43:07',
      'raw_node' => '4d21b30e940277410fcb9c82b4dfc6e4578719fa',
      'message' => 'Did something',
      'size' => '-1',
      'author' => 'kakaiba',
      'parents' => Array
      (
        '0' => '53e6539e2c62',
        '1' => 'ba42d5e5421f'
      ),

      'raw_author' => 'Maris Reyes <kakaiba@gmail.com>',
      'revision' => NULL
    )
  ),

  'canon_url' => 'https://bitbucket.org',
  'user' => 'kakaiba'

);

/**
 * Deploys BitBucket git repos
 */
class BitBucket_Deploy extends Deploy {
  /**
   * Decodes and validates the data from bitbucket and calls the
   * doploy contructor to deoploy the new code.
   *
   * @param   string  $payload  The JSON encoded payload data.
   */
  function __construct( $payload ) {
    // $payload = json_decode( stripslashes( $payload ), true );

    $repo_name = $payload['repository']['slug'];

    $this->log( '===== START =====' );
    $this->log( 'Received a BitBucket payload for "' . $payload['canon_url'] . $payload['repository']['absolute_url'] . '"' );
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
      foreach ( $payload['commits'] as $key => $commit ) {
        if ( $commit['branch'] == $deployment['branch'] ) {
          // Flag this deployment for processing
          $to_process[$name] = $deployment;
        }
      }
    }
    parent::$deployments = $to_process;

    foreach ( parent::$deployments as $name => $deployment ) {
      parent::__construct( $name, $deployment );
    }
  }
}
// Start the deploy attempt.
echo '<pre>'. "\n";
$_POST['payload'] = $payload;
new BitBucket_Deploy( $_POST['payload'] );
echo "\n" . '</pre>';
