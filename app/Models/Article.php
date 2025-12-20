<?php

namespace App\Models;

use DateTimeInterface;
use App\Models\ArticleKeyword;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;

class Article extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'articles';


    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const ARTICLE_STATUS = [
        '1' => 'Pending',
        '2' => 'Reviewing',
        '3' => 'Published',
    ];

    public const ACCESS_TYPE = [
        '1' => 'Open Access',
        '2' => 'Close Access',
    ];

    protected $fillable = [
        'journal_id',
        'member_id',
        'access_type',
        'title',
        'article_category_id',
        'article_sub_category_id',
        'author_name',
        'other_authors',
        'corresponding_authors',
        'institute_organization',
        'amount',
        'doi_link',
        'volume',
        'issue_no',
        'publish_date',
        'published_online',
        'is_recommended',
        'storage_disk',
        'file_path',
        'galley_proof_path',
        'galley_proof_status',
        'final_version_path',
        'article_status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'is_recommended' => 'boolean',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function views()
    {
        return $this->hasOne(ViewArticle::class);
    }

    public function downloads()
    {
        return $this->hasOne(DownloadArticle::class);
    }

    public function purchasedArticle()
    {
        return $this->hasMany(PurchasedArticle::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class, 'article_id', 'id')->latest();
    }
    /**
     * Scope a query to only include published Artciles that
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublish($query)
    {
        return $query->where('article_status', 3);
    }

    public function sub_articles()
    {
        return $this->hasMany(SubArticle::class, 'article_id', 'id');
    }
    public function amount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value * 100,
            set: fn ($value) => $value / 100,
        );
    }
    public function getLastAttribute()
    {
        return $this->sub_articles->last();
    }

    public function getLastPaperReviewDoc()
    {
        $file = $this->comments?->first()?->correction_upload;
        if (!$file) {
            return '';
        }
        $filename = $file->file_name;
        $storageFile = Storage::path('public/' . $file->id . '/' . $filename);
        return $storageFile;
    }

    public function pdfPaper()
    {
        $paper = $this->file_path ? Storage::disk($this->storage_disk)->get($this->file_path) : $this->convertPaperToPdf();
        return $paper;
    }

    private function convertPaperToPdf()
    {
        $file = $this->last->upload_paper;
        if (!$file) {
            return '';
        }
        $filename = $file->file_name;
        $storageFile = Storage::path('public/' . $file->id . '/' . $filename);

        $name = $file->uuid;

        /* Set the PDF Engine Renderer Path */
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');

        //Load word file
        $Content = IOFactory::load($storageFile);

        // $file->move($path, $name);

        //Save it into PDF
        $storage_path = storage_path('download/' . $name . '.pdf');
        $PDFWriter = IOFactory::createWriter($Content, 'PDF');
        $PDFWriter->save($storage_path);
        $content = file_get_contents($storage_path);
        unlink($storage_path);
        return $content;
    }

    protected function paperSize(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($this->file_path && Storage::disk($this->storage_disk)->exists($this->file_path)) {
                    $size = Storage::disk($this->storage_disk)->size($this->file_path) / 1024;
                } elseif ($this->last && $this->last->upload_paper) {
                    $size = $this->last->upload_paper->size / 1024;
                } else {
                    $size = 0;
                }
                $size = round($size, 1) >= 1024 ? round($size / 1024, 2) . " MB" : round($size, 1) . " KB";
                return $size;
            },
        );
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function article_category()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    public function journal_category()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_sub_category_id');
    }

    // ========================================
    // SPRINT 2: JOURNAL CONTEXT RELATIONSHIPS
    // ========================================

    /**
     * Get the journal this article belongs to
     */
    public function journal()
    {
        return $this->belongsTo(ArticleCategory::class, 'journal_id');
    }

    /**
     * Get the editorial board for this article's journal
     */
    public function editorialBoard()
    {
        return $this->hasManyThrough(
            JournalEditorialBoard::class,
            ArticleCategory::class,
            'id',           // article_categories.id
            'journal_id',   // editorial_boards.journal_id
            'journal_id',   // articles.journal_id
            'id'            // article_categories.id
        )->where('is_active', true);
    }

    /**
     * Get journal memberships related to this article's journal
     */
    public function journalMemberships()
    {
        return $this->hasManyThrough(
            JournalMembership::class,
            ArticleCategory::class,
            'id',           // article_categories.id
            'journal_id',   // journal_memberships.journal_id
            'journal_id',   // articles.journal_id
            'id'            // article_categories.id
        );
    }

    // ========================================
    // SPRINT 2: JOURNAL SCOPES
    // ========================================

    /**
     * Scope to filter articles by journal
     */
    public function scopeForJournal($query, $journalId)
    {
        return $query->where('journal_id', $journalId);
    }

    /**
     * Scope to filter articles by journal slug
     */
    public function scopeForJournalSlug($query, $slug)
    {
        return $query->whereHas('journal', function ($q) use ($slug) {
            $q->where('journal_slug', $slug);
        });
    }

    /**
     * Scope to filter articles by journal acronym
     */
    public function scopeForJournalAcronym($query, $acronym)
    {
        return $query->whereHas('journal', function ($q) use ($acronym) {
            $q->where('journal_acronym', $acronym);
        });
    }

    /**
     * Scope to get articles without journal assignment
     */
    public function scopeWithoutJournal($query)
    {
        return $query->whereNull('journal_id');
    }

    /**
     * Scope to get articles with journal assignment
     */
    public function scopeWithJournal($query)
    {
        return $query->whereNotNull('journal_id');
    }

    // ========================================
    // SPRINT 2: JOURNAL HELPER METHODS
    // ========================================

    /**
     * Check if article belongs to a specific journal
     */
    public function belongsToJournal($journalId): bool
    {
        return $this->journal_id == $journalId;
    }

    /**
     * Check if article has a journal assigned
     */
    public function hasJournal(): bool
    {
        return !is_null($this->journal_id);
    }

    /**
     * Get journal name
     */
    public function getJournalNameAttribute()
    {
        return $this->journal ? $this->journal->name : null;
    }

    /**
     * Get journal acronym
     */
    public function getJournalAcronymAttribute()
    {
        return $this->journal ? $this->journal->journal_acronym : null;
    }

    /**
     * Assign article to a journal
     */
    public function assignToJournal($journalId)
    {
        $this->update(['journal_id' => $journalId]);
    }

    /**
     * Remove journal assignment
     */
    public function removeJournalAssignment()
    {
        $this->update(['journal_id' => null]);
    }

    public function editor_accept()
    {
        return $this->hasOne(EditorAccept::class)->latest();
    }

    public function publisher_accept()
    {
        return $this->hasOne(PublisherAccept::class)->latest();
    }

    public function reviewer_accept()
    {
        return $this->hasOne(ReviewerAccept::class)->latest();
    }

    public function reviewer_accept_final()
    {
        return $this->hasOne(ReviewerAcceptFinal::class)->latest();
    }

    // ========================================
    // SPRINT 5: EDITORIAL WORKFLOW RELATIONSHIPS
    // ========================================

    /**
     * Get the editorial progress for this article
     */
    public function editorialProgress()
    {
        return $this->hasOne(ArticleEditorialProgress::class);
    }

    /**
     * Get the current editorial workflow
     */
    public function currentEditorialWorkflow()
    {
        return $this->belongsTo(EditorialWorkflow::class, 'editorial_workflow_id');
    }

    public function article_keywords(): BelongsToMany
    {
        return $this->belongsToMany(ArticleKeyword::class, 'article_article_keyword', 'article_id', 'article_keyword_id');
    }

    public function publishedOnline(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return Carbon::parse($this->published_online  ?? $this->created_at);
            }
        );
    }
}
