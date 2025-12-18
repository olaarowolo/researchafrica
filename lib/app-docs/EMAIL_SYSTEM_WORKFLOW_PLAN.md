# Research Africa Email System & Workflow Plan

## Executive Summary

This document outlines the comprehensive email notification system for Research Africa's multi-journal publishing platform. The system is designed to facilitate seamless communication across all stakeholders throughout the editorial workflow, ensuring timely updates and maintaining transparency in the publishing process.

## Current Email System Analysis

### Existing Email Classes (14 Total)

The current system includes 14 specialized email classes handling different aspects of the publishing workflow:

#### **1. Editorial Workflow Emails**

-   **EditorMail**: Notifies editors of new article submissions
-   **ReviewerMail**: Alerts reviewers of assigned articles
-   **PublisherMail**: Notifies publishers of articles ready for publication
-   **AcceptedMail**: Confirms article acceptance to authors
-   **PublishArticle**: Announces published articles

#### **2. System Communication Emails**

-   **ArticleMail**: General article submission confirmations
-   **NewArticle**: New article notifications
-   **ForwardedArticle**: Article forwarding between editors
-   **CommentMail**: Article comments and feedback

#### **3. User Management Emails**

-   **EmailVerification**: User account verification
-   **ResetPassword**: Password reset functionality
-   **ContactUsMail**: General inquiries and contact forms

#### **4. AfriScribe Service Emails**

-   **QuoteRequestMail**: Service quote requests
-   **QuoteRequestClientAcknowledgementMail**: Client confirmations

## Email Workflow Architecture

### **Current Workflow Stages**

#### **Stage 1: Article Submission**

```php
// Triggered when author submits article
1. ArticleMail → Author: "Submission Received"
2. EditorMail → Assigned Editor: "New Article Alert"
```

#### **Stage 2: Editorial Review**

```php
// Editor reviews and forwards
3. ForwardedArticle → Editor: "Article Forwarded"
4. ReviewerMail → Reviewer: "New Article Alert"
```

#### **Stage 3: Review Process**

```php
// Reviewer provides feedback
5. CommentMail → Author & Editor: "Review Comments"
6. AcceptedMail → Author: "Article Accepted" (if approved)
```

#### **Stage 4: Publication**

```php
// Article published
7. PublishArticle → All Stakeholders: "Article Published"
```

### **Email Template Structure**

```php
// Standard email template components
class Mailable
{
    use Queueable, SerializesModels;

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Journal Acronym] Subject Line',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.template-name',
        );
    }
}
```

## Multi-Journal Email System Enhancement Plan

### **Sprint 1: Foundation (Weeks 1-3)**

#### **1.1 Journal Context Integration**

```php
// Enhanced Email Classes with Journal Context
class JournalAwareEmail extends Mailable
{
    protected $journal;
    protected $article;
    protected $recipient;

    public function __construct($article, $recipient, $journal = null)
    {
        $this->article = $article;
        $this->recipient = $recipient;
        $this->journal = $journal ?? $this->resolveJournalContext();
    }

    private function resolveJournalContext()
    {
        if ($this->article && $this->article->journal) {
            return $this->article->journal;
        }
        return app('current_journal');
    }
}
```

#### **1.2 Acronym-Based Subject Lines**

```php
// Dynamic subject lines with journal acronyms
class EditorMail extends JournalAwareEmail
{
    public function envelope(): Envelope
    {
        $acronym = $this->journal ? $this->journal->journal_acronym : 'RESA';

        return new Envelope(
            subject: "[{$acronym}] New Article Alert: {$this->article->title}",
        );
    }
}
```

#### **1.3 Email Template Localization**

```php
// Journal-specific email templates
resources/views/mail/
├── templates/
│   ├── default/           // Default templates
│   ├── MRJ/              // Medical Research Journal
│   ├── ERJ/              // Engineering Research Journal
│   └── SJR/              // Science Journal Review
```

### **Sprint 2: Advanced Email Management (Weeks 4-6)**

#### **2.1 Journal-Specific Email Settings**

```php
// Database schema for email preferences
CREATE TABLE journal_email_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_id BIGINT UNSIGNED NOT NULL,
    email_enabled BOOLEAN DEFAULT TRUE,
    notification_triggers JSON,
    custom_templates JSON,
    branding_config JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (journal_id) REFERENCES article_categories(id)
);

// Model implementation
class JournalEmailSettings extends Model
{
    protected $fillable = [
        'journal_id',
        'email_enabled',
        'notification_triggers',
        'custom_templates',
        'branding_config'
    ];

    public function getNotificationTriggersAttribute($value)
    {
        return json_decode($value, true) ?? $this->getDefaultTriggers();
    }
}
```

#### **2.2 Automated Email Orchestration**

```php
// Email orchestration service
class EmailOrchestrationService
{
    public function triggerWorkflowEmail($stage, $article, $recipients, $journal = null)
    {
        $emailConfig = $this->getEmailConfiguration($journal);

        if (!$emailConfig['email_enabled']) {
            return; // Email disabled for this journal
        }

        $triggers = $emailConfig['notification_triggers'];

        if (in_array($stage, $triggers)) {
            $this->sendStageEmail($stage, $article, $recipients, $journal);
        }
    }

    private function sendStageEmail($stage, $article, $recipients, $journal)
    {
        $emailClass = $this->getEmailClassForStage($stage);

        foreach ($recipients as $recipient) {
            Mail::to($recipient['email'])
                ->send(new $emailClass($article, $recipient, $journal));
        }
    }
}
```

#### **2.3 Personalization Engine**

```php
// Dynamic email personalization
class EmailPersonalizationService
{
    public function personalizeContent($content, $article, $recipient, $journal)
    {
        $replacements = [
            '{{author_name}}' => $recipient['name'],
            '{{article_title}}' => $article->title,
            '{{journal_name}}' => $journal->display_name ?? $journal->name,
            '{{journal_acronym}}' => $journal->journal_acronym,
            '{{submission_date}}' => $article->created_at->format('Y-m-d'),
            '{{review_deadline}}' => $this->calculateReviewDeadline($article),
            '{{journal_url}}' => $this->generateJournalUrl($journal),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
```

### **Sprint 3: Advanced Email Features (Weeks 7-9)**

#### **3.1 Multi-Channel Notifications**

```php
// Multi-channel notification system
class MultiChannelNotificationService
{
    public function sendNotification($type, $data, $journal, $preferences = [])
    {
        $channels = $preferences['channels'] ?? ['email', 'dashboard'];

        foreach ($channels as $channel) {
            switch ($channel) {
                case 'email':
                    $this->sendEmail($type, $data, $journal);
                    break;

                case 'dashboard':
                    $this->createDashboardNotification($type, $data, $journal);
                    break;

                case 'sms':
                    $this->sendSMS($type, $data, $journal);
                    break;

                case 'push':
                    $this->sendPushNotification($type, $data, $journal);
                    break;
            }
        }
    }
}
```

#### **3.2 Email Analytics & Tracking**

```php
// Email tracking and analytics
class EmailTrackingService
{
    protected $fillable = [
        'journal_id',
        'email_type',
        'recipient_id',
        'article_id',
        'status', // sent, delivered, opened, clicked, bounced
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'metadata'
    ];

    public function trackEmailSent($journalId, $type, $recipient, $article)
    {
        return EmailTracking::create([
            'journal_id' => $journalId,
            'email_type' => $type,
            'recipient_id' => $recipient->id,
            'article_id' => $article->id,
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => json_encode([
                'recipient_email' => $recipient->email,
                'article_title' => $article->title
            ])
        ]);
    }
}
```

#### **3.3 Smart Email Templates**

```php
// Dynamic template system
class SmartEmailTemplate
{
    public function render($template, $data, $journal)
    {
        $templatePath = "mail.templates.{$journal->journal_acronym}.{$template}";

        if (!view()->exists($templatePath)) {
            $templatePath = "mail.templates.default.{$template}";
        }

        return view($templatePath, $data)->render();
    }

    public function getAvailableTemplates($journal)
    {
        $defaultTemplates = ['editor-alert', 'reviewer-notification', 'author-confirmation'];
        $journalTemplates = "templates/{$journal->journal_acronym}/";

        return array_merge($defaultTemplates, $this->getJournalSpecificTemplates($journalTemplates));
    }
}
```

## Enhanced Email Workflow Scenarios

### **Scenario 1: Article Submission to Medical Research Journal (MRJ)**

#### **Step 1: Author Submits Article**

```php
// URL: /journals/MRJ/submit
$journal = ArticleCategory::where('journal_acronym', 'MRJ')->first();
$article = Article::create([...]);

// Automatic email sequence
EmailOrchestrationService::triggerWorkflowEmail('submission', $article, [
    ['email' => $author->email, 'name' => $author->fullname],
    ['email' => $journal->editor_email, 'name' => 'Editor-in-Chief']
], $journal);
```

#### **Generated Emails:**

1. **Author Confirmation**

    - Subject: `[MRJ] Submission Received: "Your Article Title"`
    - Template: `mail.templates.MRJ.author-confirmation`
    - Contains: Journal branding, submission details, next steps

2. **Editor Alert**
    - Subject: `[MRJ] New Article Alert: "Your Article Title"`
    - Template: `mail.templates.MRJ.editor-alert`
    - Contains: Article details, review guidelines, submission timeline

### **Scenario 2: Review Process Communication**

#### **Reviewer Assignment**

```php
// Reviewer gets assigned
EmailOrchestrationService::triggerWorkflowEmail('reviewer_assigned', $article, [
    ['email' => $reviewer->email, 'name' => $reviewer->fullname]
], $journal);
```

#### **Generated Email:**

-   **Reviewer Notification**
    -   Subject: `[MRJ] Review Assignment: "Your Article Title"`
    -   Template: `mail.templates.MRJ.reviewer-notification`
    -   Contains: Article abstract, review criteria, deadline, submission link

#### **Review Completion**

```php
// Review submitted
EmailOrchestrationService::triggerWorkflowEmail('review_completed', $article, [
    ['email' => $editor->email, 'name' => $editor->fullname],
    ['email' => $author->email, 'name' => $author->fullname]
], $journal);
```

#### **Generated Emails:**

1. **Editor Notification**: Review completed, recommendations
2. **Author Notification**: Review feedback, next steps

### **Scenario 3: Publication Announcement**

#### **Article Published**

```php
// Article published
EmailOrchestrationService::triggerWorkflowEmail('published', $article, [
    ['email' => $author->email, 'name' => $author->fullname],
    ['email' => $editor->email, 'name' => $editor->fullname],
    ['email' => $subscribers, 'name' => 'Journal Subscribers']
], $journal);
```

#### **Generated Email:**

-   **Publication Announcement**
    -   Subject: `[MRJ] New Publication: "Your Article Title"`
    -   Template: `mail.templates.MRJ.publication-announcement`
    -   Contains: Article link, citation details, journal branding

## Email Template Design System

### **Template Structure**

```html
<!-- Example: mail.templates.MRJ.author-confirmation.blade.php -->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>{{ $journal->display_name }} - Submission Confirmation</title>
        <style>
            /* Journal-specific styling */
            .journal-header {
                background-color: {{ $journal->branding->primary_color ?? '#2c5282' }};
            }
            .journal-logo {
                max-height: 60px;
            }
        </style>
    </head>
    <body>
        <div class="journal-header">
            <img
                src="{{ $journal->branding->logo_url }}"
                alt="{{ $journal->display_name }}"
                class="journal-logo"
            />
            <h1>{{ $journal->display_name }}</h1>
        </div>

        <div class="content">
            <h2>Submission Confirmation</h2>
            <p>Dear {{ $author_name }},</p>

            <p>
                We have received your article submission for consideration in {{
                $journal->display_name }}:
            </p>

            <div class="article-details">
                <h3>{{ $article_title }}</h3>
                <p><strong>Submission ID:</strong> {{ $submission_id }}</p>
                <p><strong>Submitted on:</strong> {{ $submission_date }}</p>
                <p>
                    <strong>Journal:</strong> {{ $journal->display_name }} ({{
                    $journal->journal_acronym }})
                </p>
            </div>

            <div class="next-steps">
                <h3>What happens next?</h3>
                <ol>
                    <li>Editorial review (2-3 weeks)</li>
                    <li>Peer review process (4-6 weeks)</li>
                    <li>Editorial decision (1-2 weeks)</li>
                    <li>Publication (if accepted)</li>
                </ol>
            </div>

            <div class="actions">
                <a href="{{ $tracking_url }}" class="button"
                    >Track Your Submission</a
                >
                <a href="{{ $journal_url }}" class="button"
                    >Visit Journal Website</a
                >
            </div>
        </div>

        <div class="footer">
            <p>
                {{ $journal->display_name }} | {{ $journal->journal_acronym }}
            </p>
            <p>
                <a href="{{ $unsubscribe_url }}"
                    >Unsubscribe from notifications</a
                >
            </p>
        </div>
    </body>
</html>
```

### **Responsive Email Design**

```css
/* Mobile-first responsive design */
@media (max-width: 600px) {
    .journal-header {
        padding: 20px 10px;
    }

    .journal-logo {
        max-height: 40px;
    }

    .content {
        padding: 15px;
    }

    .article-details {
        background: #f7fafc;
        padding: 15px;
        border-radius: 8px;
    }

    .button {
        display: block;
        width: 100%;
        margin: 10px 0;
        text-align: center;
    }
}
```

## Email Delivery Infrastructure

### **Queue Management**

```php
// Queue configuration for email delivery
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
    ],

    'emails' => [
        'driver' => 'redis',
        'connection' => 'emails',
        'queue' => 'high',
        'retry_after' => 90,
    ],
];

// Email job with retry logic
class SendJournalEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    public function handle()
    {
        try {
            Mail::to($this->recipient)->send($this->mailable);

            // Log successful delivery
            $this->logDelivery('delivered');

        } catch (Exception $e) {
            // Log failed delivery
            $this->logDelivery('failed', $e->getMessage());

            // Release back to queue for retry
            $this->release(30);
        }
    }
}
```

### **Delivery Monitoring**

```php
// Email delivery monitoring
class EmailDeliveryMonitor
{
    public function monitorDelivery($emailId)
    {
        $email = EmailTracking::find($emailId);

        // Check delivery status via webhook or polling
        $status = $this->checkDeliveryStatus($email->provider_message_id);

        $email->update([
            'status' => $status['status'],
            'delivered_at' => $status['delivered_at'],
            'opened_at' => $status['opened_at'] ?? null,
            'clicked_at' => $status['clicked_at'] ?? null,
        ]);

        // Trigger follow-up actions
        if ($status['status'] === 'bounced') {
            $this->handleBounce($email);
        }
    }
}
```

## Email Analytics & Performance

### **Key Metrics Tracking**

```php
// Email analytics dashboard
class EmailAnalyticsService
{
    public function getJournalEmailMetrics($journalId, $period = '30days')
    {
        return [
            'total_sent' => EmailTracking::where('journal_id', $journalId)
                                        ->where('sent_at', '>=', now()->subDays(30))
                                        ->count(),

            'delivery_rate' => $this->calculateDeliveryRate($journalId, $period),
            'open_rate' => $this->calculateOpenRate($journalId, $period),
            'click_rate' => $this->calculateClickRate($journalId, $period),
            'bounce_rate' => $this->calculateBounceRate($journalId, $period),

            'top_performing_emails' => $this->getTopPerformingTemplates($journalId, $period),
            'email_volume_by_type' => $this->getEmailVolumeByType($journalId, $period),
        ];
    }
}
```

### **Performance Optimization**

```php
// Email performance optimization
class EmailPerformanceOptimizer
{
    public function optimizeDeliverySchedule($journalId)
    {
        $analytics = $this->getJournalEmailMetrics($journalId);

        // Determine optimal send times based on engagement
        $optimalTimes = $this->calculateOptimalTimes($analytics);

        return [
            'recommended_send_times' => $optimalTimes,
            'batch_size_recommendations' => $this->getBatchSizeRecommendations($analytics),
            'template_improvements' => $this->getTemplateImprovements($analytics),
        ];
    }
}
```

## Security & Compliance

### **Email Security Measures**

```php
// Email security implementation
class SecureEmailService
{
    public function sendSecureEmail($email, $data, $journal)
    {
        // SPF/DKIM validation
        $this->validateEmailAuth($journal);

        // Rate limiting
        $this->enforceRateLimit($email, $journal);

        // Content filtering
        $this->scanContent($data['content']);

        // Send with encryption
        return $this->sendEncrypted($email, $data, $journal);
    }
}
```

### **GDPR Compliance**

```php
// GDPR compliance for email communications
class GDPRCompliantEmailService
{
    public function sendWithConsent($email, $template, $data, $journal)
    {
        // Check user consent
        $userConsent = $this->checkEmailConsent($email, $journal->id);

        if (!$userConsent) {
            throw new EmailConsentException('User has not consented to receive emails');
        }

        // Include unsubscribe link
        $data['unsubscribe_token'] = $this->generateUnsubscribeToken($email, $journal->id);

        return $this->send($email, $template, $data);
    }
}
```

## Integration with External Services

### **Email Service Providers**

```php
// Multi-provider email service
class EmailServiceProvider
{
    protected $providers = [
        'ses' => AmazonSESService::class,
        'sendgrid' => SendGridService::class,
        'mailgun' => MailgunService::class,
        'postmark' => PostmarkService::class,
    ];

    public function send($provider, $email, $template, $data)
    {
        $service = new $this->providers[$provider]();

        return $service->send($email, $template, $data);
    }

    public function failover($originalProvider, $email, $template, $data)
    {
        $providers = array_keys($this->providers);
        $currentIndex = array_search($originalProvider, $providers);

        for ($i = $currentIndex + 1; $i < count($providers); $i++) {
            try {
                return $this->send($providers[$i], $email, $template, $data);
            } catch (Exception $e) {
                continue; // Try next provider
            }
        }

        throw new EmailDeliveryException('All email providers failed');
    }
}
```

## Implementation Timeline

### **Phase 1: Foundation (Weeks 1-3)**

-   ✅ Implement journal context in email classes
-   ✅ Create acronym-based subject lines
-   ✅ Set up basic template structure

### **Phase 2: Enhancement (Weeks 4-6)**

-   ✅ Build email configuration system
-   ✅ Implement orchestration service
-   ✅ Create personalization engine

### **Phase 3: Advanced Features (Weeks 7-9)**

-   ✅ Multi-channel notifications
-   ✅ Email analytics and tracking
-   ✅ Smart template system

### **Phase 4: Optimization (Weeks 10-12)**

-   ✅ Performance optimization
-   ✅ Security enhancements
-   ✅ Integration testing

## Success Metrics

### **Technical Metrics**

-   **Email Delivery Rate**: >95%
-   **Email Open Rate**: >25% (industry average: 20%)
-   **Email Click Rate**: >5% (industry average: 3%)
-   **Bounce Rate**: <2%

### **Business Metrics**

-   **User Satisfaction**: >4.5/5 rating for email communications
-   **Response Time**: <24 hours for email-based inquiries
-   **Workflow Efficiency**: 30% reduction in manual communication

### **Journal-Specific Metrics**

-   **Email Engagement**: Monitored per journal
-   **Template Performance**: A/B testing results
-   **Notification Effectiveness**: Workflow completion rates

## Conclusion

The enhanced email system for Research Africa's multi-journal platform will provide:

✅ **Journal-Specific Communication** with acronym-based identification
✅ **Automated Workflow Notifications** throughout the editorial process
✅ **Personalized Email Content** based on user roles and preferences
✅ **Multi-Channel Support** for comprehensive communication
✅ **Advanced Analytics** for performance monitoring and optimization
✅ **Robust Security & Compliance** measures for data protection

This system will significantly improve communication efficiency, enhance user experience, and support the growth of multiple journals within the Research Africa ecosystem.

The email system will be seamlessly integrated with the URL-based routing architecture, ensuring that every communication includes relevant journal context and maintains consistency across all touchpoints.
