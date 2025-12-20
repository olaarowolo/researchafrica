<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Mail\ArticleMail;
use App\Mail\EditorMail;
use App\Mail\ReviewerMail;
use App\Mail\NewArticle;
use App\Mail\PublishArticle;
use App\Mail\ContactUsMail;
use App\Models\Article;
use App\Models\Member;
use App\Models\ArticleCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailSendingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->seedBasicData();
    }

    protected function seedBasicData()
    {
        // Create basic required data
        \App\Models\Country::factory()->create(['name' => 'Nigeria']);
        \App\Models\MemberType::factory()->create(['name' => 'Author']);
        \App\Models\MemberType::factory()->create(['name' => 'Editor']);
        \App\Models\MemberType::factory()->create(['name' => 'Reviewer']);
        \App\Models\MemberRole::factory()->create(['title' => 'Author']);
        \App\Models\MemberRole::factory()->create(['title' => 'Editor']);
        \App\Models\MemberRole::factory()->create(['title' => 'Reviewer']);
    }

    /** @test */
    public function it_sends_article_submission_mail()
    {
        // Arrange
        $member = Member::factory()->create([
            'email_address' => 'author@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $category = ArticleCategory::factory()->create([
            'name' => 'Computer Science',
            'slug' => 'computer-science'
        ]);

        $article = Article::factory()->create([
            'member_id' => $member->id,
            'article_category_id' => $category->id,
            'title' => 'Test Article Submission',
            'article_status' => 1 // Pending
        ]);

        // Act - Send article submission mail
        Mail::to($member->email_address)->send(new ArticleMail($article));


        // Assert
        Mail::assertSent(ArticleMail::class, function ($mail) use ($article, $member) {
            return $mail->article->id === $article->id &&
                   $mail->hasTo($member->email_address);
        });
    }

    /** @test */
    public function it_sends_editor_assignment_mail()
    {
        // Arrange
        $author = Member::factory()->create([
            'email_address' => 'author@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Smith'
        ]);

        $editor = Member::factory()->create([
            'email_address' => 'editor@example.com',
            'first_name' => 'Dr. Editor',
            'last_name' => 'Johnson'
        ]);

        $category = ArticleCategory::factory()->create([
            'name' => 'Medicine Journal',
            'is_journal' => true
        ]);

        $article = Article::factory()->create([
            'member_id' => $author->id,
            'article_category_id' => $category->id,
            'journal_id' => $category->id,
            'title' => 'Medical Research Article',
            'article_status' => 2 // Under Review
        ]);

        // Act - Send editor assignment mail
        Mail::to($editor->email_address)->send(new EditorMail($article, $editor));


        // Assert
        Mail::assertSent(EditorMail::class, function ($mail) use ($article, $editor) {
            return $mail->article->id === $article->id &&
                   $mail->editor->id === $editor->id &&
                   $mail->hasTo($editor->email_address);
        });
    }

    /** @test */
    public function it_sends_reviewer_assignment_mail()
    {
        // Arrange
        $author = Member::factory()->create([
            'email_address' => 'author@example.com',
            'first_name' => 'Alice',
            'last_name' => 'Brown'
        ]);

        $reviewer = Member::factory()->create([
            'email_address' => 'reviewer@example.com',
            'first_name' => 'Prof',
            'last_name' => 'Reviewer'
        ]);

        $category = ArticleCategory::factory()->create([
            'name' => 'Engineering Journal',
            'is_journal' => true
        ]);

        $article = Article::factory()->create([
            'member_id' => $author->id,
            'article_category_id' => $category->id,
            'journal_id' => $category->id,
            'title' => 'Engineering Study Article',
            'article_status' => 2 // Under Review
        ]);

        // Act - Send reviewer assignment mail
        Mail::to($reviewer->email_address)->send(new ReviewerMail($article, $reviewer));


        // Assert
        Mail::assertSent(ReviewerMail::class, function ($mail) use ($article, $reviewer) {
            return $mail->article->id === $article->id &&
                   $mail->reviewer->id === $reviewer->id &&
                   $mail->hasTo($reviewer->email_address);
        });
    }

    /** @test */
    public function it_sends_new_article_notification_mail()
    {
        // Arrange
        $author = Member::factory()->create([
            'email_address' => 'author@example.com',
            'first_name' => 'Bob',
            'last_name' => 'Wilson'
        ]);

        $category = ArticleCategory::factory()->create([
            'name' => 'Science Journal',
            'slug' => 'science-journal'
        ]);

        $article = Article::factory()->create([
            'member_id' => $author->id,
            'article_category_id' => $category->id,
            'title' => 'New Scientific Discovery',
            'article_status' => 3 // Published
        ]);

        // Act - Send new article notification
        Mail::to($author->email_address)->send(new NewArticle($article));


        // Assert
        Mail::assertSent(NewArticle::class, function ($mail) use ($article, $author) {
            return $mail->article->id === $article->id &&
                   $mail->hasTo($author->email_address);
        });
    }

    /** @test */
    public function it_sends_publication_mail()
    {
        // Arrange
        $author = Member::factory()->create([
            'email_address' => 'author@example.com',
            'first_name' => 'Carol',
            'last_name' => 'Davis'
        ]);

        $category = ArticleCategory::factory()->create([
            'name' => 'Technology Journal',
            'slug' => 'tech-journal'
        ]);

        $article = Article::factory()->create([
            'member_id' => $author->id,
            'article_category_id' => $category->id,
            'title' => 'AI Research Article',
            'article_status' => 3 // Published
        ]);

        // Act - Send publication mail
        Mail::to($author->email_address)->send(new PublishArticle($article));

        // Assert
        Mail::assertSent(PublishArticle::class, function ($mail) use ($article) {
            return $mail->article->id === $article->id;
        });

        Mail::assertSent(PublishArticle::class, function ($mail) use ($author) {
            return $mail->hasTo($author->email_address);
        });
    }

    /** @test */
    public function it_sends_contact_us_mail()
    {
        // Arrange
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Inquiry about submission',
            'message' => 'I would like to know more about the submission process.'
        ];

        // Act - Send contact us mail
        Mail::to('contact@researchafrica.com')->send(new ContactUsMail($contactData));

        // Assert
        Mail::assertSent(ContactUsMail::class, function ($mail) use ($contactData) {
            return $mail->data['name'] === $contactData['name'] &&
                   $mail->data['email'] === $contactData['email'] &&
                   $mail->data['subject'] === $contactData['subject'] &&
                   $mail->data['message'] === $contactData['message'];
        });
    }

    /** @test */
    public function it_handles_mail_queue_correctly()
    {
        // Arrange
        $member = Member::factory()->create([
            'email_address' => 'queue@example.com'
        ]);

        $article = Article::factory()->create([
            'member_id' => $member->id,
            'title' => 'Queue Test Article'
        ]);

        // Act - Send mail using queue
        Mail::to($member->email_address)->queue(new ArticleMail($article));

        // Assert
        Mail::assertQueued(ArticleMail::class, function ($mail) use ($article) {
            return $mail->article->id === $article->id;
        });

        Mail::assertQueued(ArticleMail::class, function ($mail) use ($member) {
            return $mail->hasTo($member->email_address);
        });
    }

    /** @test */
    public function it_handles_multiple_mail_recipients()
    {
        // Arrange
        $author = Member::factory()->create(['email_address' => 'author@example.com']);
        $editor = Member::factory()->create(['email_address' => 'editor@example.com']);

        $article = Article::factory()->create(['member_id' => $author->id]);

        // Act - Send mail to multiple recipients
        Mail::to([$author->email_address, $editor->email_address])
            ->send(new ArticleMail($article));

        // Assert
        Mail::assertSent(ArticleMail::class, 2);
        Mail::assertSent(ArticleMail::class, function ($mail) use ($author) {
            return $mail->hasTo($author->email_address);
        });
        Mail::assertSent(ArticleMail::class, function ($mail) use ($editor) {
            return $mail->hasTo($editor->email_address);
        });
    }

    /** @test */
    public function it_handles_mail_failure_gracefully()
    {
        // Arrange
        $member = Member::factory()->create([
            'email_address' => 'invalid-email'
        ]);

        $article = Article::factory()->create(['member_id' => $member->id]);

        // Act - Try to send mail to invalid address
        try {
            Mail::to($member->email_address)->send(new ArticleMail($article));
        } catch (\Exception $e) {
            // Expected to fail with invalid email
        }

        // Assert - Mail should not be sent
        Mail::assertNotSent(ArticleMail::class);
    }

    /** @test */
    public function it_verifies_mail_content_structure()
    {
        // Arrange
        $member = Member::factory()->create([
            'email_address' => 'content@example.com',
            'first_name' => 'Test',
            'last_name' => 'User'
        ]);

        $article = Article::factory()->create([
            'member_id' => $member->id,
            'title' => 'Content Structure Test'
        ]);

        // Act - Send mail and capture content
        $mailable = new ArticleMail($article);
        $rendered = $mailable->render();

        // Assert - Verify mail content structure
        $this->assertStringContainsString($article->title, $rendered);
        $this->assertStringContainsString($member->first_name, $rendered);
        $this->assertStringContainsString($member->last_name, $rendered);
    }

    /** @test */
    public function it_handles_bcc_mail_correctly()
    {
        // Arrange
        $member = Member::factory()->create(['email_address' => 'bcc@example.com']);
        $adminEmail = 'admin@researchafrica.com';

        $article = Article::factory()->create(['member_id' => $member->id]);

        // Act - Send mail with BCC
        Mail::to($member->email_address)
            ->bcc($adminEmail)
            ->send(new ArticleMail($article));

        // Assert
        Mail::assertSent(ArticleMail::class, function ($mail) use ($member, $adminEmail) {
            return $mail->hasTo($member->email_address) &&
                   $mail->hasBcc($adminEmail);
        });
    }

    /** @test */
    public function it_respects_mail_throttling()
    {
        // Arrange
        $member = Member::factory()->create(['email_address' => 'throttle@example.com']);
        $article = Article::factory()->create(['member_id' => $member->id]);

        // Act - Send multiple mails rapidly
        for ($i = 0; $i < 5; $i++) {
            Mail::to($member->email_address)->send(new ArticleMail($article));
        }

        // Assert - All mails should be sent
        Mail::assertSent(ArticleMail::class, 5);
    }

    /** @test */
    public function it_logs_mail_sending_activity()
    {
        // Arrange
        $member = Member::factory()->create(['email_address' => 'log@example.com']);
        $article = Article::factory()->create(['member_id' => $member->id]);

        // Act - Send mail
        Log::shouldReceive('info')
            ->once()
            ->with('Mail sent successfully', \Mockery::on(function ($context) use ($article, $member) {
                return isset($context['article_id']) &&
                       isset($context['recipient']) &&
                       $context['article_id'] === $article->id &&
                       $context['recipient'] === $member->email_address;
            }));

        Mail::to($member->email_address)->send(new ArticleMail($article));

        // Assert - Mail was sent
        Mail::assertSent(ArticleMail::class);
    }
}
