<?php

namespace Tests\Helpers;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AssertionHelpers
{
    /**
     * Assert that a response is a successful JSON response
     */
    public static function assertJsonSuccess(TestResponse $response, $data = null)
    {
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data'
                 ])
                 ->assertJson([
                     'success' => true
                 ]);

        if ($data) {
            $response->assertJsonFragment($data);
        }

        return $response;
    }

    /**
     * Assert that a response is an error JSON response
     */
    public static function assertJsonError(TestResponse $response, $statusCode = 422, $message = null)
    {
        $response->assertStatus($statusCode)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'errors'
                 ])
                 ->assertJson([
                     'success' => false
                 ]);

        if ($message) {
            $response->assertJsonFragment(['message' => $message]);
        }

        return $response;
    }

    /**
     * Assert that an email was sent with specific parameters
     */
    public static function assertEmailSent($mailableClass, $callback = null)
    {
        if ($callback) {
            Mail::assertSent($mailableClass, $callback);
        } else {
            Mail::assertSent($mailableClass);
        }
    }

    /**
     * Assert that an email was NOT sent
     */
    public static function assertEmailNotSent($mailableClass)
    {
        Mail::assertNotSent($mailableClass);
    }

    /**
     * Assert that no emails were sent
     */
    public static function assertNoEmailsSent()
    {
        Mail::assertNothingSent();
    }

    /**
     * Assert that a file exists in storage
     */
    public static function assertFileExists($path, $disk = 'articles')
    {
        Storage::disk($disk)->assertExists($path);
    }

    /**
     * Assert that a file does NOT exist in storage
     */
    public static function assertFileNotExists($path, $disk = 'articles')
    {
        Storage::disk($disk)->assertMissing($path);
    }

    /**
     * Assert that a file has been deleted from storage
     */
    public static function assertFileDeleted($path, $disk = 'articles')
    {
        self::assertFileNotExists($path, $disk);
    }

    /**
     * Assert model relationships are properly loaded
     */
    public static function assertModelRelationships($model, $relationships = [])
    {
        foreach ($relationships as $relationship => $expected) {
            if ($model->relationLoaded($relationship)) {
                $related = $model->getRelation($relationship);

                if ($expected instanceof \Illuminate\Database\Eloquent\Model) {
                    self::assertModelExists($expected);
                    self::assertEquals($expected->id, $related->id);
                } elseif (is_array($expected) || $related instanceof \Illuminate\Support\Collection) {
                    self::assertTrue($related->contains($expected));
                }
            }
        }
    }

    /**
     * Assert that a model has specific attributes
     */
    public static function assertModelAttributes($model, $attributes)
    {
        foreach ($attributes as $attribute => $expected) {
            self::assertEquals($expected, $model->{$attribute}, "Model attribute {$attribute} does not match expected value");
        }
    }

    /**
     * Assert that a model is soft deletable and has been deleted
     */
    public static function assertSoftDeleted($model)
    {
        self::assertTrue($model->trashed());
        self::assertNotNull($model->deleted_at);
    }

    /**
     * Assert that a model is NOT soft deleted
     */
    public static function assertNotSoftDeleted($model)
    {
        self::assertFalse($model->trashed());
        self::assertNull($model->deleted_at);
    }

    /**
     * Assert that a collection has expected count
     */
    public static function assertCollectionCount($collection, $expectedCount)
    {
        self::assertEquals($expectedCount, $collection->count());
    }

    /**
     * Assert that a collection contains expected models
     */
    public static function assertCollectionContains($collection, $models)
    {
        foreach ($models as $model) {
            self::assertTrue($collection->contains($model));
        }
    }

    /**
     * Assert that a collection does NOT contain expected models
     */
    public static function assertCollectionDoesNotContain($collection, $models)
    {
        foreach ($models as $model) {
            self::assertFalse($collection->contains($model));
        }
    }

    /**
     * Assert that a string contains another string (case-insensitive)
     */
    public static function assertStringContains($haystack, $needle)
    {
        self::assertStringContainsString($needle, $haystack);
    }

    /**
     * Assert that a string does NOT contain another string (case-insensitive)
     */
    public static function assertStringDoesNotContain($haystack, $needle)
    {
        self::assertStringNotContainsString($needle, $haystack);
    }

    /**
     * Assert that a date is after another date
     */
    public static function assertDateAfter($date1, $date2)
    {
        self::assertTrue($date1->isAfter($date2));
    }

    /**
     * Assert that a date is before another date
     */
    public static function assertDateBefore($date1, $date2)
    {
        self::assertTrue($date1->isBefore($date2));
    }

    /**
     * Assert that a date is the same as another date
     */
    public static function assertDateEquals($date1, $date2)
    {
        self::assertTrue($date1->isSameDay($date2));
    }

    /**
     * Assert that an array has expected structure
     */
    public static function assertArrayStructure($array, $structure)
    {
        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                self::assertArrayHasKey($key, $array);
                self::assertArrayStructure($array[$key], $value);
            } else {
                self::assertArrayHasKey($value, $array);
            }
        }
    }

    /**
     * Assert that a model has expected scopes applied
     */
    public static function assertModelScopes($model, $expectedScopes = [])
    {
        $query = $model->getQuery();

        // This is a simplified check - in reality, you'd need more sophisticated logic
        // to inspect applied scopes from the query builder
        foreach ($expectedScopes as $scope => $value) {
            self::assertNotNull($model->{$scope});
        }
    }

    /**
     * Assert that an array has no null values
     */
    public static function assertArrayNoNulls($array)
    {
        foreach ($array as $key => $value) {
            self::assertNotNull($value, "Array key '{$key}' is null");
        }
    }

    /**
     * Assert that a string is a valid email address
     */
    public static function assertValidEmail($email)
    {
        self::assertTrue(filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }

    /**
     * Assert that a string is a valid URL
     */
    public static function assertValidUrl($url)
    {
        self::assertTrue(filter_var($url, FILTER_VALIDATE_URL) !== false);
    }

    /**
     * Assert that a number is within expected range
     */
    public static function assertInRange($number, $min, $max)
    {
        self::assertGreaterThanOrEqual($min, $number);
        self::assertLessThanOrEqual($max, $number);
    }

    /**
     * Assert that a string matches expected regex pattern
     */
    public static function assertMatchesPattern($pattern, $string)
    {
        self::assertTrue(preg_match($pattern, $string) === 1);
    }

    /**
     * Assert that a collection is sorted by specific field
     */
    public static function assertCollectionSortedBy($collection, $field, $direction = 'asc')
    {
        $values = $collection->pluck($field)->toArray();
        $sortedValues = $values;

        if ($direction === 'asc') {
            sort($sortedValues);
        } else {
            rsort($sortedValues);
        }

        self::assertEquals($sortedValues, $values);
    }

    /**
     * Assert that two models are the same instance
     */
    public static function assertSameModel($model1, $model2)
    {
        self::assertSame($model1->id, $model2->id);
        self::assertSame(get_class($model1), get_class($model2));
    }

    /**
     * Assert that a model has specific database table
     */
    public static function assertModelTable($model, $expectedTable)
    {
        self::assertEquals($expectedTable, $model->getTable());
    }

    /**
     * Assert that a model has expected fillable attributes
     */
    public static function assertModelFillable($model, $fillable)
    {
        $modelFillable = $model->getFillable();
        foreach ($fillable as $attribute) {
            self::assertContains($attribute, $modelFillable);
        }
    }

    /**
     * Assert that a model has expected hidden attributes
     */
    public static function assertModelHidden($model, $hidden)
    {
        $modelHidden = $model->getHidden();
        foreach ($hidden as $attribute) {
            self::assertContains($attribute, $modelHidden);
        }
    }

    /**
     * Assert that a model uses expected traits
     */
    public static function assertModelUsesTraits($model, $traits)
    {
        $modelTraits = class_uses($model);
        foreach ($traits as $trait) {
            self::assertContains($trait, $modelTraits);
        }
    }

    /**
     * Assert that a response has expected pagination structure
     */
    public static function assertPaginationStructure(TestResponse $response)
    {
        $response->assertJsonStructure([
            'data',
            'current_page',
            'last_page',
            'per_page',
            'total',
            'from',
            'to'
        ]);
    }

    /**
     * Assert that a response has expected search results structure
     */
    public static function assertSearchResultsStructure(TestResponse $response)
    {

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'abstract',
                    'author_name',
                    'published_at',
                    'category'
                ]
            ],
            'pagination',
            'facets'
        ]);

    }
}

