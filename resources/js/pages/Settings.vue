<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Form, usePage } from '@inertiajs/vue3';
import { PageProps } from '@inertiajs/core';
import { yandex_url } from '@/routes/settings/update';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { ref } from 'vue';

let example_url = "https://yandex.ru/maps/org/samoye_populyarnoye_kafe/1010501395/reviews/"

type SettingsPageProps = {
  yandex_url: string | undefined | null; 
} & PageProps

const page = usePage<SettingsPageProps>();
const inputUrl = ref(page.props.yandex_url || '');
</script>

<template>

    <Head title="Настройка" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <h1>Подключить Яндекс</h1>
            <div v-if="page.props.flash.success" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                <p class="font-bold">Успех!</p>
                <p>{{ page.props.flash.success }}</p>
            </div>
            <Form v-bind="yandex_url.form()" :reset-on-success="['password']" v-slot="{ errors, processing }"
                class="flex flex-col gap-6">
                <div class="grid gap-2">
                    <div class="grid gap-2">
                        <Label for="yandex_url">
                            Укажите ссылку на Яндекс, пример
                            <a target="_blank" :href="example_url">{{ example_url }}</a>
                        </Label>
                        <Input id="yandex_url" v-model="inputUrl" type="url" name="yandex_url" required autofocus :tabindex="1" autocomplete="false" />
                        <InputError :message="errors.yandex_url" />
                    </div>
                    <div class="flex justify-start">

                        <Button type="submit" class="w-auto mt-4" :tabindex="4" :disabled="processing" size="sm">
                            <Spinner v-if="processing" />
                            Сохранить
                        </Button>
                    </div>
                </div>

            </Form>
        </div>
    </AppLayout>
</template>
