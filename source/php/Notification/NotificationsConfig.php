<?php

namespace VolunteerManager\Notification;

/**
 * Notification subject, content and rules
 *
 * No logic should be added here, only configuration.
 * Subject and content only supported in Swedish language. Not required to be translated.
 */
class NotificationsConfig
{
    public static function getNotifications()
    {
        return [
            "Message to submitter when externally created assignments is approved" => [
                'key' => 'external_assignment_approved',
                'taxonomy' => 'assignment-status',
                'oldValue' => 'pending',
                'newValue' => 'approved',
                'message' => [
                    'subject' => 'Ditt uppdrag "%s" har godkänts',
                    'content' => 'Hej %s,<br><br>Så kul att du vill engagera fler helsingborgare! Ditt uppdrag “%s” är nu godkänt för publicering på <a href="https://helsingborg.se">helsingborg.se</a> och kommer bli synligt för många engagerade helsingborgare. Skulle du vilja ändra, uppdatera något eller avpublicera uppdraget så skicka ett mejl till engagemang@helsingborg.se.<br><br>Lycka till!<br><br>Med vänliga hälsningar,<br>Engagemang Helsingborg',
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to submitter when externally created assignments is denied" => [
                'key' => 'external_assignment_denied',
                'taxonomy' => 'assignment-status',
                'oldValue' => 'pending',
                'newValue' => 'denied',
                'message' => [
                    'subject' => 'Ditt uppdrag "%s" har nekats',
                    'content' => 'Hej %s,<br><br>Tack för att du vill registrera ett uppdrag för engagerade helsingborgare. Uppdraget "%s" har behandlats av handläggare och tyvärr så kan det inte publiceras. Kontakta Engagemang Helsingborg för mer information på engagemang@helsingborg.se.<br><br>Med vänliga hälsningar,<br>Engagemang Helsingborg',
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to admin when a new assignment is created" => [
                'key' => 'admin_external_assignment_new',
                'taxonomy' => 'assignment-status',
                'oldValue' => '',
                'newValue' => 'pending',
                'message' => [
                    'subject' => 'Ett nytt uppdrag har skapats',
                    'content' => 'Hej,<br><br>Ett nytt volontäruppdrag ”%s” har skapats i vårt system och är redo att behandlas.<br>%s<br><br>Med vänliga hälsningar,<br><br>Engagemang Helsingborg',
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to the volunteer when a new volunteer is created" => [
                'key' => 'external_volunteer_new',
                'taxonomy' => 'employee-registration-status',
                'oldValue' => '',
                'newValue' => 'new',
                'message' => [
                    'subject' => 'Din ansökan har tagits emot',
                    'content' => 'Hej %s,<br><br>Vi är glada att meddela dig att din ansökan om att bli volontär har mottagits och vi är tacksamma för ditt intresse i att hjälpa till att göra en positiv skillnad i vår stad. Tack för att du tar dig tid att engagera dig för andra! Du har tagit ett steg mot att göra Helsingborg till en bättre plats och vi är tacksamma för din vilja att hjälpa till.<br><br><b>Boka tid för ett volontärsamtal</b><br>Då du inte har varit volontär genom Helsingborgs stad innan så vill vi gärna träffa dig för att prata om dina önskemål och förväntningar om att vara volontär samt kunna berätta mer vad det innebär att vara volontär genom Helsingborg stad.<br><br>Du bokar en tid för ett samtal genom e-tjänsten på länken nedan.<br>Boka volontärsamtal här: <a href="https://eservice.helsingborg.se/H379">https://eservice.helsingborg.se/H379</a>.<br><br>För att hålla dig uppdaterad om nya volontäruppdrag och få inbjudningar till events så registrera dig gärna i nyhetsbrevet där vi löpande skickar ut information.<br>Du anmäler dig till nyhetsbrevet här: <a href="https://gansub.com/s/KeBa8RD">https://gansub.com/s/KeBa8RD</a><br><br>Vi vill också informera dig om att din ansökan har registreinsats i vårt system och kommer att behandlas inom kort. Tills dess, känn dig fri att kontakta oss om du har några frågor eller funderingar.<br><br>Tack igen för ditt engagemang!<br><br>Med vänliga hälsningar,<br><br>Engagemang Helsingborg',
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to the volunteer when a new volunteer is approved" => [
                'key' => 'external_volunteer_approved',
                'taxonomy' => 'employee-registration-status',
                'oldValue' => 'new',
                'newValue' => 'approved',
                'message' => [
                    'subject' => 'Din ansökan har godkänts',
                    'content' => 'Hej %s,<br><br>Så kul att du vill vara volontär! Din ansökan som volontär i Helsingborg har blivit godkänd. Välkommen till vårt team av engagerade människor som vill göra skillnad i samhället.<br><br>Att vara volontär ger dig möjlighet att berika ditt liv samtidigt som du hjälper och påverkar andra positivt. Genom ditt engagemang blir du en viktig förebild och en viktig tillgång för dem vi stödjer.<br><br>Tillsammans kan vi skapa verklig förändring och göra skillnad i människors liv.<br>Nu kan du gå in och anmäla dig till ett uppdrag som passar dig.<br>Du hittar alla uppdrag på denna sida: <a href="https://helsingborg.se/volontaruppdrag">Volontäruppdrag | Helsingborg.se</a><br><br>Med vänliga hälsningar,<br>Engagemang Helsingborg',
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to the volunteer when a new volunteer is denied" => [
                'key' => 'external_volunteer_denied',
                'taxonomy' => 'employee-registration-status',
                'oldValue' => 'new',
                'newValue' => 'denied',
                'message' => [
                    'subject' => 'Din ansökan har inte blivit godkänd',
                    'content' => 'Hej %s,<br><br>Tack så mycket för ditt intresse att bli volontär i Helsingborg.<br><br>Tyvärr måste vi meddela att din ansökan som volontär inte har blivit godkänd.<br><br>Kontakta oss gärna för att vet mer om din ansökan genom att mejla engagemang@helsingborg.se<br><br>Med vänliga hälsningar,<br>Engagemang Helsingborg',
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to admin when a new volunteer is created" => [
                'key' => 'admin_external_volunteer_new',
                'taxonomy' => 'employee-registration-status',
                'oldValue' => '',
                'newValue' => 'new',
                'message' => [
                    'subject' => 'En ny ansökan har skapats',
                    'content' => 'Hej,<br><br>En ny ansökan har skapats i vårt system och är redo att behandlas. Ansökan gäller en ny volontär som vill hjälpa till att göra en positiv skillnad i Helsingborg.<br><br>Vi ber dig vänligen att ta en titt på ansökan och att behandla den så snart som möjligt. Vi vill se till att varje volontär som är intresserad av att hjälpa till att förbättra vår stad får en chans att göra det.<br><br>Med vänliga hälsningar,<br><br>Engagemang Helsingborg',
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to admin when a new application for an assignment is created" => [
                'key' => 'admin_external_application_new',
                'taxonomy' => 'application-status',
                'oldValue' => '',
                'newValue' => 'pending',
                'message' => [
                    'subject' => 'En ny ansökan till volontäruppdrag har skapats',
                    'content' => 'Hej,<br><br>En ny ansökan till volontäruppdraget ”%s” har skapats i vårt system och är redo att behandlas.<br>%s<br><br>Med vänliga hälsningar,<br><br>Engagemang Helsingborg',
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to the volunteer when a new application for an assignment is created" => [
                'key' => 'external_application_new',
                'taxonomy' => 'application-status',
                'oldValue' => '',
                'newValue' => 'pending',
                'message' => [
                    'subject' => 'Din ansökan har tagits emot',
                    'content' => 'Hej %s,<br><br>Tack för din anmälan till uppdraget “%s”. Inom kort kommer du få besked om din anmälan blivit godkänd.<br><br>Med vänliga hälsningar,<br><br>Engagemang Helsingborg',
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to the volunteer when a new application for an assignment is approved" => [
                'key' => 'external_application_approved',
                'taxonomy' => 'application-status',
                'oldValue' => 'pending',
                'newValue' => 'approved',
                'message' => [
                    'subject' => 'Din ansökan har godkänts',
                    'content' => 'Hej %s,<br><br>Din ansökan till uppdraget "%s" är nu godkänd. Du kommer bli kontaktad i ett separat mejl med instruktioner och förutsättningar för uppdraget. Har du frågor kan du alltid kontakta oss på engagemang@helsingborg.se eller på 042-105000.<br>Lycka till med ditt uppdrag och tack för att du gör skillnad!<br><br>Med vänliga hälsningar,<br><br>Engagemang Helsingborg',
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to the volunteer when a new application for an assignment is approved, with condition" => [
                'key' => 'external_application_approved_condition',
                'taxonomy' => 'application-status',
                'oldValue' => 'pending',
                'newValue' => 'approved_with_condition',
                'message' => [
                    'subject' => 'Din ansökan har godkänts',
                    'content' => 'Hej %s,<br><br>Din ansökan till uppdraget "%s" är nu godkänd, men behöver kompletteras med ytterligare information. Du kommer bli kontaktad i ett separat mejl gällande kompletteringen samt instruktioner och förutsättningar för uppdraget. Har du frågor kan du alltid kontakta oss på engagemang@helsingborg.se eller på 042-105000.<br>Lycka till med ditt uppdrag och tack för att du gör skillnad!<br><br>Med vänliga hälsningar,<br><br>Engagemang Helsingborg',
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
            "Message to the volunteer when a new application for an assignment is denied" => [
                'key' => 'external_application_denied',
                'taxonomy' => 'application-status',
                'oldValue' => 'pending',
                'newValue' => 'denied',
                'message' => [
                    'subject' => 'Din ansökan har nekats',
                    'content' => 'Hej %s,<br><br>Din ansökan till ”%s” har inte godkänts. Engagemang Helsingborg kommer att kontakta dig och berätta varför. Har du övriga frågor är du välkommen att höra av dig till kontaktcenter@helsingborg.se eller på 042-105000.<br>Tack!<br><br>Med vänliga hälsningar,<br><br>Engagemang Helsingborg',
                ],
                'rule' => [
                    'key' => 'source',
                    'value' => '',
                    'operator' => 'NOT_EQUAL'
                ]
            ],
        ];
    }
}
