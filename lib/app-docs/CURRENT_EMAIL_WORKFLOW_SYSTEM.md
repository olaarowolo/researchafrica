# Research Africa Current Email Workflow System

## Executive Summary

This document outlines the **CURRENTLY IMPLEMENTED** email notification system in the Research Africa platform. It analyzes the existing 14 email classes and their functionality as they exist in the codebase today.

## Current Email System Overview

### **Implemented Email Classes (14 Total)**

The current system includes 14 email classes that handle basic communication throughout the editorial workflow:

#### **1. Editorial Workflow Emails**

**EditorMail**

-   **Purpose**: Notifies editors of new article submissions
-   **Trigger**: When article is submitted
-   **Recipients**: Assigned editor
-   **Subject**: "A New Article Alert"
-   **Template**: `mail.editor-email`

**ReviewerMail**

-   **Purpose**: Alerts reviewers of assigned articles
-   **Trigger**: When reviewer is assigned to article
-   **Recipients**: Assigned reviewer
-   **Subject**: "New Article Alert"
-   **Template**: `mail.reviewer-email`

**AcceptedMail** (Stage-Based System)

-   **Purpose**: Stage-based article acceptance notifications
-   **Stages**: 5 distinct stages with different templates
-   **Status Mapping**:
    -   Status 2 → Stage 1 → Template: `accepted-mail-stage1`
    -   Status 4 → Stage 2 → Template: `accepted-mail-stage2`
    -   Status 6 → Stage 3 → Template: `accepted-mail-stage3`
    -   Status 12 → Stage 4 → Template: `accepted-mail-stage4`
    -   Status 10 → Stage 5 → Template: `accepted-mail-stage5`
-   **Subject**: "Accepted Article - Stage [1-5]"

**PublishArticle**

-   **Purpose**: Announces published articles
-   **Trigger**: When article is published
-   **Recipients**: Author and stakeholders
-   **Subject**: "Published Article: [Article Title]"
-   **Template**: `accepted-mail-stage5`

#### **2. System Communication Emails**

**ArticleMail**

-   **Purpose**: General article submission confirmations
-   **Trigger**: When article is submitted
-   **Recipients**: Submitting author
-   **Subject**: "Submission Received"
-   **Template**: `mail.article-mail`

**NewArticle**

-   **Purpose**: New article notifications
-   **Trigger**: When new article is created
-   **Recipients**: Editor and relevant stakeholders
-   **Subject**: "A New Article Alert"
-   **Template**: `mail.new-article`

**CommentMail**

-   **Purpose**: Article comments and feedback notifications
-   **Trigger**: When new comment is added to article
-   **Recipients**: Article author and relevant parties
-   **Subject**: "A New Comment"
-   **Template**: `mail.comment-mail`

#### **3. User Management Emails**

**EmailVerification**

-   **Purpose**: User account verification emails
-   **Trigger**: During user registration
-   **Recipients**: New users
-   **Functionality**: Contains verification tokens

**ResetPassword**

-   **Purpose**: Password reset functionality
-   **Trigger**: When user requests password reset
-   **Recipients**: Users requesting password reset
-   **Functionality**: Contains reset tokens and links

**ContactUsMail**

-   **Purpose**: General inquiries and contact form submissions
-   **Trigger**: When contact form is submitted
-   **Recipients**: System administrators
-   **Functionality**: Forwards contact form data

#### **4. AfriScribe Service Emails**

**QuoteRequestMail**

-   **Purpose**: Service quote requests
-   **Trigger**: When user requests AfriScribe service quote
-   **Recipients**: AfriScribe service team
-   **Functionality**: Contains quote request details

**QuoteRequestClientAcknowledgementMail**

-   **Purpose**: Client confirmation of quote requests
-   **Trigger**: When quote request is submitted
-   **Recipients**: Client who made the request
-   **Functionality**: Acknowledges receipt of quote request

#### **5. Additional System Emails**

**PublisherMail**

-   **Purpose**: Publisher notifications
-   **Trigger**: When articles are ready for publication
-   **Recipients**: Publisher team
-   **Functionality**: Publication workflow notifications

**ForwardedArticle**

-   **Purpose**: Article forwarding between editors
-   **Trigger**: When articles are forwarded between editorial staff
-   **Recipients**: Receiving editor
-   **Functionality**: Inter-editor communication

## Current Email Workflow Implementation

### **Basic Editorial Workflow (Current)**

#### **Stage 1: Article Submission**

```php
// Triggered when author submits article
1. ArticleMail → Author: "Submission Received"
2. EditorMail → Assigned Editor: "A New Article Alert"
```

#### **Stage 2: Editorial Review**

```php
// Editor reviews and forwards
3. NewArticle → Editor: "A New Article Alert" (if forwarded)
4. ReviewerMail → Reviewer: "New Article Alert"
```

#### **Stage 3: Review Process**

```php
// Reviewer provides feedback
5. CommentMail → Author & Editor: "A New Comment"
6. AcceptedMail → Author: "Accepted Article - Stage X" (if approved)
```

#### **Stage 4: Publication**

```php
// Article published
7. PublishArticle → All Stakeholders: "Published Article: [Title]"
```

## Current System Limitations

### **1. No Journal Context**

-   All emails use generic subject lines
-   No journal acronym or branding
-   Same templates used for all articles regardless of journal

### **2. Limited Personalization**

-   Basic variable replacement
-   No dynamic content based on user preferences
-   Fixed email templates

### **3. Basic Workflow**

-   Simple trigger-based emails
-   No sophisticated orchestration
-   Limited workflow state management

### **4. No Analytics**

-   No email tracking or analytics
-   No delivery confirmation
-   No performance monitoring

### **5. Basic Template System**

-   Static HTML templates
-   No responsive design
-   Limited customization options

## Current Email Queue Configuration

### **Queue Implementation**

```php
// All emails implement ShouldQueue for background processing
class EditorMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    // Email implementation
}
```

### **Queue Settings**

-   **Background Processing**: All emails processed via Laravel queues
-   **Default Connection**: Uses application default queue connection
-   **Retry Logic**: Laravel default retry mechanism
-   **No Prioritization**: All emails treated equally

## Current Workflow Status

### **Implemented Features**

✅ **Basic Email Classes**: 14 specialized email classes
✅ **Queue Processing**: Background email processing
✅ **Template System**: Basic HTML email templates
✅ **User Verification**: Email verification workflow
✅ **Password Reset**: Password reset functionality
✅ **Contact System**: Contact form email forwarding
✅ **Editorial Alerts**: Basic editorial workflow notifications

### **Missing Features**

❌ **Journal Context**: No journal-specific email content
❌ **Advanced Personalization**: Limited dynamic content
❌ **Email Analytics**: No tracking or performance metrics
❌ **Multi-Channel Support**: Email only (no SMS/push notifications)
❌ **Template Management**: No admin interface for template management
❌ **Performance Optimization**: No email performance monitoring
❌ **Compliance Features**: No GDPR or compliance tracking

## Conclusion

The current Research Africa email system provides basic functionality for editorial workflow communication but lacks the sophistication needed for a multi-journal platform. It successfully handles:

✅ **Core Communication**: Essential editorial workflow emails
✅ **User Management**: Account verification and password reset
✅ **Queue Processing**: Background email delivery
✅ **Basic Templates**: Simple email layouts

However, it requires significant enhancement to support the multi-journal transformation outlined in the sprint plan, including:

🔄 **Journal Context Integration**: Acronym-based identification
🔄 **Advanced Personalization**: Dynamic content and templates
🔄 **Analytics & Tracking**: Performance monitoring
🔄 **Multi-Channel Support**: Comprehensive notification system
🔄 **Template Management**: Admin-controlled email customization

The current system serves as a solid foundation that will be enhanced according to the sprint plan to create a world-class multi-journal email communication system.
