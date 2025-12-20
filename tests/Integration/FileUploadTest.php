<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Member;
use App\Models\ArticleCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('articles');
        $this->seedBasicData();
    }

    protected function seedBasicData()
    {
        \App\Models\Country::factory()->create(['name' => 'Nigeria']);
        \App\Models\MemberType::factory()->create(['name' => 'Author']);
        \App\Models\MemberRole::factory()->create(['title' => 'Author']);
    }

    /** @test */
    public function it_can_create_articles_with_file_uploads()
    {
        // Arrange
        $member = Member::factory()->create();
        $category = ArticleCategory::factory()->create();

        $file = UploadedFile::fake()->create('article.pdf', 1024);

        // Act
        $response = $this->actingAs($member, 'member')
            ->post('/member/articles', [
                'title' => 'Test PDF Article',
                'article_category_id' => $category->id,
                'file' => $file,
                'article_status' => 1
            ]);

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('articles', [
            'title' => 'Test PDF Article',
            'member_id' => $member->id
        ]);

        // Verify file was stored
        $article = Article::where('title', 'Test PDF Article')->first();
        $this->assertNotNull($article->file_path);
        $this->assertTrue(Storage::disk('articles')->exists($article->file_path));
    }

    /** @test */
    public function it_validates_file_types()
    {
        // Arrange
        $member = Member::factory()->create();
        $category = ArticleCategory::factory()->create();

        $file = UploadedFile::fake()->create('document.docx', 512);

        // Act
        $response = $this->actingAs($member, 'member')
            ->post('/member/articles', [
                'title' => 'Invalid File Article',
                'article_category_id' => $category->id,
                'file' => $file,
                'article_status' => 1
            ]);

        // Assert
        $response->assertSessionHasErrors(['file']);
    }

    /** @test */
    public function it_handles_file_deletion()
    {
        // Arrange
        $member = Member::factory()->create();
        $category = ArticleCategory::factory()->create();

        $file = UploadedFile::fake()->create('delete-test.pdf', 1024);

        $article = Article::create([
            'member_id' => $member->id,
            'article_category_id' => $category->id,
            'title' => 'Delete Test Article',
            'file_path' => 'test-file.pdf',
            'article_status' => 1
        ]);

        Storage::disk('articles')->put($article->file_path, 'test content');

        // Act
        $article->delete();

        // Assert
        $this->assertFalse(Storage::disk('articles')->exists($article->file_path));
    }
}
