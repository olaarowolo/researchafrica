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
        'article_status',
        'created_at',
        'updated_at',
        'deleted_at',
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
