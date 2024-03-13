<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL);


// =====================================================================
// Helfer
// =====================================================================
function TAG($tag, $text=NULL)
{
    echo "<$tag>$text</$tag>";
}

function P($text)
{
    TAG("P", $text);
}

function LI($text)
{
    TAG("LI", $text);
}


function dump($ldapConn)
{
    TAG("p", "Alle OUs:");

    try {
        $ouSearch = ldap_search($ldapConn, 'dc=localhost', '(objectClass=organizationalUnit)');
        if($ouSearch)
        {
            $ouEntries = ldap_get_entries($ldapConn, $ouSearch);

            echo "<ul>";
            foreach ($ouEntries as $ouEntry) {
                LI($ouEntry['dn']);
            }
            echo "</ul>";
        }
        else
        {
            echo "<ul>";
            LI("---");
            echo "</ul>";
        }
    }
    catch(\Exception $e)
    {
        var_dump($e);
    }
    
    # ------------------------------------------------------------------
    
    TAG("p", "Alle Personen:");
    
    try {
        $personSearch = ldap_search($ldapConn, 'ou=people,dc=localhost', '(objectclass=posixAccount)');
        if($personSearch)
        {
            $personEntries = ldap_get_entries($ldapConn, $personSearch);
            
            echo "<ul>";
            foreach ($personEntries as $personEntry) {
                LI($personEntry['dn']);
            }
            echo "</ul>";
        }
        else
        {
            echo "<ul>";
            LI("---");
            echo "</ul>";
        }
    }
    catch(\Exception $e)
    {
        var_dump($e);
    }
}




// =====================================================================
// LDAP-Server-Konfiguration
// =====================================================================

function connect()
{
    $LDAP_SERVER = 'ldap://openldap';
    $LDAP_PORT = 389;

    // Verbindung zum LDAP-Server herstellen
    $ldapConn = ldap_connect($LDAP_SERVER, $LDAP_PORT);
    if (!$ldapConn) {
        die("Kann keine Verbindung zum LDAP-Server herstellen.");
    }

    // Optionen setzen (falls erforderlich)
    ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
    
    return $ldapConn;
}



// =====================================================================
// Anonyme Bindung
// =====================================================================
function anon_test($ldapConn)
{
    if (ldap_bind($ldapConn)) {
        TAG("p", "<b>Anonyme Bindung erfolgreich.</b>");
        dump($ldapConn);
    } else {
        TAG("p", "<b>Anonyme Bindung fehlgeschlagen.</b>");
    }
    TAG('HR');
}




// =====================================================================
// Bindung als uid=user1000,ou=people,dc=localhost
// =====================================================================

function user_test($ldapConn)
{
    $bindUser = 'uid=user1000,ou=people,dc=localhost';
    $bindPassword = 'dontaskme'; // Passwort hier einfügen

    if (ldap_bind($ldapConn, $bindUser, $bindPassword)) {
        TAG("p", "<b>Bindung als user1000 erfolgreich.</b>");
        dump($ldapConn);
    } else {
        TAG("p", "<b>Bindung als user1000 fehlgeschlagen.</b>");
    }
    TAG('HR');
}






// =====================================================================
// Bindung als cn=admin,dc=localhost
// =====================================================================

function admin_test($ldapConn)
{
    $adminUser = 'cn=admin,dc=localhost';
    $adminPassword = 'dontaskme'; // Passwort hier einfügen

    if (ldap_bind($ldapConn, $adminUser, $adminPassword)) {
        TAG("p", "<b>Bindung als Admin erfolgreich.</b>");
        dump($ldapConn);
    } else {
        TAG("p", "<b>Bindung als Admin fehlgeschlagen.</b>");
    }
    TAG('HR');
}


// =====================================================================
// RUN
// =====================================================================

$ldapConn = connect();

admin_test($ldapConn);
user_test($ldapConn);
anon_test($ldapConn);

ldap_close($ldapConn);
