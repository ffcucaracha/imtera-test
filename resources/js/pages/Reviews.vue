<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { PageProps } from '@inertiajs/core';
import StarRating from '@/components/StarRating.vue';
import Review from '@/components/Review.vue';
import { ref } from 'vue';
import YIcon from '@/components/YIcon.vue';

export type ReviewType = {
    id: number;
    author: string;
    phone: string;
    text?: string;
    rating: number;
    published_at?: string;
    title?: string
}

type ReviewsPageProps = {
    reviews: ReviewType[],
    total_count: number,
    total_rating: number,
    error?: string
} & PageProps

const blockHeight = ref("155px")
const page = usePage<ReviewsPageProps>();
</script>

<template>

    <Head title="Отзывы" />

    <AppLayout>
        <div class="flex pl-4">
            <div class="flex border border-gray-300 rounded-xl items-center p-2 mt-4 ml-4">
                <YIcon />&nbsp;Яндекс Карты
            </div>
        </div>
        <div class="flex h-full flex-1 flex-col-reverse md:flex-row gap-3 overflow-x-auto p-4 pt-0">
            <div v-if="page.props.error" class="mt-4 w-full bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md"
                role="alert">
                <p class="font-bold">Ошибка!</p>
                <p>{{ page.props.error }}</p>
            </div>
            <template v-else>
                <div class="w-full md:w-4/5 p-4">
                    <div class="flex flex-col gap-4">
                        <div v-for="review in page.props.reviews" :key="review.id">
                            <Review :id="review.id" :author="review.author" :phone="review.phone" :text="review.text"
                                :rating="review.rating" :published_at="review.published_at" :title="review.title" />
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/5 p-4 ">
                    <div :style="{ height: blockHeight, minWidth: 'fit-content' }"
                        class="border border-gray-300 rounded-xl p-6 shadow-md flex flex-col ">
                        <div
                            class="flex items-center flex-col lg:flex-row justify-between border-b border-gray-300 pb-3 mb-3 flex-grow">
                            <div class="text-4xl font-bold text-gray-800">
                                {{ page.props.total_rating }}
                            </div>
                            <StarRating :rating="page.props.total_rating" :scale="4" />
                        </div>
                        <div class="flex flex-grow items-center">
                            <p>Всего отзывов: {{ page.props.total_count }}</p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
