<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\CategoryNotificationSubscription;

use Illuminate\Notifications\DatabaseNotification;
use App\Http\Requests\CategoryNotificationSubscriptionRequest;

class NotificationController extends Controller
{
    public function userNotifications(){
       return $this->successResponse($this->getAuthUser()->notifications);

    }

    public function markAsRead(DatabaseNotification  $notification){
        $notification->markAsRead();
        return $this->successResponse(null, null, 'Notification mark as read');
    }

    public function markAsUnread(DatabaseNotification  $notification){
        $notification->markAsUnread();
        return $this->successResponse(null, null, 'Notification mark as unread');
    }

    public function deleteNotification(DatabaseNotification  $notification){
        $notification->delete();
        return $this->successResponse(null, null, 'Notification deleted');
    }

    public function subscribeCategory(CategoryNotificationSubscriptionRequest $request){
    $subscriptions = [];
        foreach ($request->category_id as $category_id) {
            $subscriptions[] = [
                'id' => Str::orderedUuid(),
                'category_id' => $category_id,
                'user_id' => auth()->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        CategoryNotificationSubscription::insert($subscriptions);
        $this->successResponse(null,'successful');

    }

    public function totalUnread(){
        return $this->successResponse($this->getAuthUser()->unreadNotifications->count(), null, 'total unread count');
    }

    public function subscribableCategory(){
        $subscribedCatagories =  $this->getAuthUser()->categoryNotificationSubscriptionId();
        $subscribableCategory = Category::whereNotIn('id', $subscribedCatagories)->where('status', true)->get(['id', 'name', 'description']);
        return $this->successResponse($subscribableCategory, null, 'subscribable categories');
    }

    public function subscribedCategory(){
        $subscribedCatagoriesId =  $this->getAuthUser()->categoryNotificationSubscriptionId();
        $subscribedCategories = Category::whereIn('id', $subscribedCatagoriesId)->where('status', true)->get(['id', 'name', 'description']);
        return $this->successResponse($subscribedCategories, null, 'subscribed categories');
    }

    public function unsubscribeCategory(CategoryNotificationSubscriptionRequest $request){
       CategoryNotificationSubscription::where('user_id', $this->getAuthUser()->id)->whereIn('category_id', $request->category_id)->delete();
        return $this->successResponse(null, null, 'un-subscription successful');
    }
}
