<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Mail\ArticleMail;
use App\Mail\EditorMail;
use App\Mail\ReviewerMail;
use App\Mail\NewArticle;
use App\Mail\PublishArticle;
use App\Mail\ContactUsMail;
use App\Mail\EmailVerification;
use App\Mail\ResetPassword;
use App\Mail\AcceptedMail;
use App\Models\Article;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class MailTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBasicData();
    }

    protected function seedBasicData()
    {
        \App\Models\Country::factory()->create(['name' => 'Nigeria']);
        \App\Models\MemberType::factory()->create(['name' => 'Author']);
        \App\Models\MemberType::factory()->create(['name' => 'Editor']);
        \App\Models\MemberType::factory()->create(['name' => 'Reviewer']);
        \App\Models\MemberRole::factory()->create(['title' => 'Author']);
        \App\Models\MemberRole::factory()->create(['title' => 'Editor']);
        \App\Models\MemberRole::factory()->create(['title' => 'Reviewer']);
    }

    /** @test */
    public function it_creates_article_mail_mailable()
    {
        // Arrange
        $fullName = 'John Doe Test User';

        // Act
        $mailable = new ArticleMail($fullName);

        // Assert
        $this->assertInstanceOf(ArticleMail::class, $mailable);
        $this->assertNotNull($mailable->full_name);
        $this->assertEquals($fullName, $mailable->full_name);
    }

    /** @test */
    public function it_creates_editor_mail_mailable()
    {
        // Arrange
        $editor = Member::factory()->create(['email_address' => 'editor@test.com']);
        $article = Article::factory()->create(['title' => 'Editor Test Article']);

        // Act
        $mailable = new EditorMail($article, $editor);

        // Assert
        $this->assertInstanceOf(EditorMail::class, $mailable);
        $this->assertNotNull($mailable->article);
        $this->assertNotNull($mailable->editor);
        $this->assertEquals($article->id, $mailable->article->id);
        $this->assertEquals($editor->id, $mailable->editor->id);
    }

    /** @test */
    public function it_creates_reviewer_mail_mailable()
    {
        // Arrange
        $reviewer = Member::factory()->create(['email_address' => 'reviewer@test.com']);
        $article = Article::factory()->create(['title' => 'Reviewer Test Article']);

        // Act
        $mailable = new ReviewerMail($article, $reviewer);

        // Assert
        $this->assertInstanceOf(ReviewerMail::class, $mailable);
        $this->assertNotNull($mailable->article);
        $this->assertNotNull($mailable->reviewer);
        $this->assertEquals($article->id, $mailable->article->id);
        $this->assertEquals($reviewer->id, $mailable->reviewer->id);
    }

    /** @test */
    public function it_creates_new_article_mail_mailable()
    {
        // Arrange
        $editor = Member::factory()->create(['email_address' => 'editor@test.com']);
        $article = Article::factory()->create([
            'title' => 'New Article',
            'article_status' => 3
        ]);

        // Act
        $mailable = new NewArticle($article, $editor);

        // Assert
        $this->assertInstanceOf(NewArticle::class, $mailable);
        $this->assertNotNull($mailable->article);
        $this->assertNotNull($mailable->editor);
        $this->assertEquals($article->id, $mailable->article->id);
        $this->assertEquals($editor->id, $mailable->editor->id);
    }

    /** @test */
    public function it_creates_publish_article_mail_mailable()
    {
        // Arrange
        $fullname = 'John Doe';
        $title = 'Published Article Title';

        // Act
        $mailable = new PublishArticle($fullname, $title);

        // Assert
        $this->assertInstanceOf(PublishArticle::class, $mailable);
        $this->assertNotNull($mailable->fullname);
        $this->assertNotNull($mailable->title);
        $this->assertEquals($fullname, $mailable->fullname);
        $this->assertEquals($title, $mailable->title);
        $this->assertEquals(10, $mailable->stage);
    }

    /** @test */
    public function it_creates_contact_us_mail_mailable()
    {
        // Arrange
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message content'
        ];

        // Act
        $mailable = new ContactUsMail($contactData);

        // Assert
        $this->assertInstanceOf(ContactUsMail::class, $mailable);
        $this->assertIsArray($mailable->data);
        $this->assertEquals($contactData['name'], $mailable->data['name']);
        $this->assertEquals($contactData['email'], $mailable->data['email']);
    }

    /** @test */
    public function it_creates_email_verification_mail_mailable()
    {
        // Arrange
        $member = Member::factory()->create(['email_address' => 'verify@test.com']);
        $token = 'test-token-123';

        // Act
        $mailable = new EmailVerification($member, $token);

        // Assert
        $this->assertInstanceOf(EmailVerification::class, $mailable);
        $this->assertNotNull($mailable->member);
        $this->assertEquals($member->id, $mailable->member->id);
        $this->assertEquals($token, $mailable->token);
    }

    /** @test */
    public function it_creates_reset_password_mail_mailable()
    {
        // Arrange
        $member = Member::factory()->create(['email_address' => 'reset@test.com']);
        $token = 'test-token-123';
        $hash = 'test-hash-456';

        // Act
        $mailable = new ResetPassword($member, $token, $hash);

        // Assert
        $this->assertInstanceOf(ResetPassword::class, $mailable);
        $this->assertNotNull($mailable->member);
        $this->assertEquals($member->id, $mailable->member->id);
        $this->assertEquals($token, $mailable->token);
        $this->assertEquals($hash, $mailable->hash);
    }

    /** @test */
    public function it_creates_accepted_mail_mailable()
    {
        // Arrange
        $fullname = 'John Doe';
        $stage = 10; // Published stage
        $title = 'Accepted Article Title';

        // Act
        $mailable = new AcceptedMail($fullname, $stage, $title);

        // Assert
        $this->assertInstanceOf(AcceptedMail::class, $mailable);
        $this->assertNotNull($mailable->fullname);
        $this->assertNotNull($mailable->stage);
        $this->assertNotNull($mailable->title);
        $this->assertEquals($fullname, $mailable->fullname);
        $this->assertEquals($stage, $mailable->status);
        $this->assertEquals(5, $mailable->stage); // Should map to stage 5
        $this->assertEquals($title, $mailable->title);
    }

    /** @test */
    public function it_sends_multiple_mail_types_in_sequence()
    {
        // Arrange
        $article = Article::factory()->create(['title' => 'Sequence Test Article']);
        $editor = Member::factory()->create(['email_address' => 'editor@test.com']);
        $reviewer = Member::factory()->create(['email_address' => 'reviewer@test.com']);

        // Act
        Mail::to('test@example.com')->send(new ArticleMail('Test User'));
        Mail::to('editor@example.com')->send(new EditorMail($article, $editor));
        Mail::to('reviewer@example.com')->send(new ReviewerMail($article, $reviewer));

        // Assert
        Mail::assertSent(ArticleMail::class, 1);
        Mail::assertSent(EditorMail::class, 1);
        Mail::assertSent(ReviewerMail::class, 1);
    }

    /** @test */
    public function it_handles_mail_with_special_characters()
    {
        // Arrange
        $fullName = 'Special Characters: <script>alert("test")</script> & "quotes"';

        // Act
        $mailable = new ArticleMail($fullName);

        // Assert
        $this->assertInstanceOf(ArticleMail::class, $mailable);
        $this->assertNotNull($mailable->full_name);
        $this->assertStringContainsString('Special Characters', $mailable->full_name);
    }

    /** @test */
    public function it_verifies_mail_subject_lines()
    {
        // Arrange
        $article = Article::factory()->create(['title' => 'Subject Test Article']);

        // Act & Assert - Test envelope subjects
        $articleMail = new ArticleMail('Test User');
        $this->assertEquals('Submission  Received', $articleMail->envelope()->subject);

        $editorMail = new EditorMail($article, Member::factory()->create());
        $this->assertEquals('A New Article Alert', $editorMail->envelope()->subject);

        $newArticleMail = new NewArticle($article, Member::factory()->create());
        $this->assertEquals('A New Article Alert', $newArticleMail->envelope()->subject);

        $publishMail = new PublishArticle('John Doe', 'Test Title');
        $this->assertEquals('Published Article: Test Title', $publishMail->envelope()->subject);

        $acceptedMail = new AcceptedMail('John Doe', 10, 'Test Article');
        $this->assertEquals('Accepted Article - Stage 5', $acceptedMail->envelope()->subject);
    }

    /** @test */
    public function it_verifies_mail_view_templates()
    {
        // Arrange
        $article = Article::factory()->create();

        // Act & Assert - Test content view templates
        $articleMail = new ArticleMail('Test User');
        $this->assertEquals('mail.article-mail', $articleMail->content()->view);

        $editorMail = new EditorMail($article, Member::factory()->create());
        $this->assertEquals('mail.editor-email', $editorMail->content()->view);

        $newArticleMail = new NewArticle($article, Member::factory()->create());
        $this->assertEquals('mail.new-article', $newArticleMail->content()->view);

        $publishMail = new PublishArticle('Test', 'Test');
        $this->assertEquals('mail.accepted-mail-stage5', $publishMail->content()->view);

        $acceptedMail = new AcceptedMail('Test', 10);
        $this->assertEquals('mail.accepted-mail-stage5', $acceptedMail->content()->view);
    }

    /** @test */
    public function it_handles_accepted_mail_stage_mapping()
    {
        // Arrange & Act & Assert - Test stage mapping
        $acceptedMailStage2 = new AcceptedMail('John Doe', 2);
        $this->assertEquals(1, $acceptedMailStage2->stage);
        $this->assertEquals('mail.accepted-mail-stage1', $acceptedMailStage2->content()->view);

        $acceptedMailStage4 = new AcceptedMail('John Doe', 4);
        $this->assertEquals(2, $acceptedMailStage4->stage);
        $this->assertEquals('mail.accepted-mail-stage2', $acceptedMailStage4->content()->view);

        $acceptedMailStage6 = new AcceptedMail('John Doe', 6);
        $this->assertEquals(3, $acceptedMailStage6->stage);
        $this->assertEquals('mail.accepted-mail-stage3', $acceptedMailStage6->content()->view);

        $acceptedMailStage12 = new AcceptedMail('John Doe', 12);
        $this->assertEquals(4, $acceptedMailStage12->stage);
        $this->assertEquals('mail.accepted-mail-stage4', $acceptedMailStage12->content()->view);
    }
}

