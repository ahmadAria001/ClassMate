<script lang="ts">
    import { Button, Input, Label, Popover, Toast } from "flowbite-svelte";
    import { QuestionCircleSolid } from "flowbite-svelte-icons";

    import { createForm } from "felte";
    import { validateSchema, validator } from "@felte/validator-zod";
    import { page } from "@inertiajs/svelte";

    import {
        type ResetSchema,
        resetSchema,
    } from "@R/Utils/Schema/User/Password/Reset";

    import axiosInstance from "axios";
    import { createEventDispatcher } from "svelte";
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    export let showState: boolean = false;

    const dispatch = createEventDispatcher();
    const axios = axiosInstance.create({ withCredentials: true });
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };
    let rebuild = {};

    const { form, isSubmitting, errors } = createForm<ResetSchema>({
        extend: validator<ResetSchema>({
            schema: resetSchema,
        }),
        onSubmit: async (values) => {
            console.log(values);
            const response = await axios.post("/api/auth/pw/reset", {
                old_password: values.old_password,
                new_password: values.new_password,
                _token: $page.props.csrf_token,
            });

            err = response.data;
            dispatch("comp");
            showState = false;
            rebuild = {};

            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);
        },
        onError: (values: unknown) => {
            err = {
                message: values?.response?.data?.message,
                status: values?.response?.data?.status,
            };
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            rebuild = {};

            return;
        },
        onSuccess: () => {
            console.log("Success");
        },
    });
</script>

<div class="dark:bg-gray-800 rounded-lg mt-7 py-6 px-3">
    {#key rebuild}
        <h5
            class="mb-4 text-xl font-bold tracking-tight text-gray-900 dark:text-white"
        >
            Informasi Password
        </h5>
        <form method="POST" use:form>
            <div class="grid md:grid-cols-2 md:gap-6">
                <div class="mb-4">
                    <Label for="currentPassword" class="mb-2"
                        >Password Saat ini</Label
                    >
                    <Input
                        id="currentPassword"
                        name="old_password"
                        type="password"
                        placeholder="Password Saat ini"
                    />
                    {#if $errors.old_password}
                        <span class="text-sm text-red-500"
                            >{$errors.old_password}</span
                        >
                    {/if}
                </div>
                <div class="mb-4">
                    <Label for="newPassword" class="mb-2">Password Baru</Label>
                    <div class="flex align-middle justify-center gap-2">
                        <Input
                            id="newPassword"
                            name="new_password"
                            type="password"
                            placeholder="Password Baru"
                        />
                        <div class="flex align-middle w-fit h-full py-2">
                            <QuestionCircleSolid
                                class="w-5 h-5 dark:fill-white"
                                id="pass-hint"
                            />
                        </div>
                    </div>
                    {#if $errors.new_password}
                        <span class="text-sm text-red-500"
                            >{$errors.new_password}</span
                        >
                    {/if}
                </div>
            </div>
            <div class="mb-4">
                <Label for="confirmPassword" class="mb-2"
                    >Konfirmasi Password</Label
                >
                <Input
                    id="confirmPassword"
                    name="confirm_pass"
                    type="password"
                    placeholder="Konfirmasi Password"
                />
                {#if $errors.confirm_pass}
                    <span class="text-sm text-red-500"
                        >{$errors.confirm_pass}</span
                    >
                {/if}
            </div>
            <div>
                <div class="block flex mt-3">
                    <Popover
                        class="w-64 text-sm text-white"
                        title="Persyaratan Password"
                        triggeredBy={`#pass-hint`}
                    >
                        <ul
                            class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400"
                        >
                            <li>
                                Minimal 8 karakter (dan hingga 100 karakter)
                            </li>
                            <li>Gunakan kombinasi huruf besar dan kecil</li>
                            <li>
                                Gunakan minimal satu karakter khusus, misalnya,
                                ! @ # ?
                            </li>
                        </ul>
                    </Popover>
                    <Button
                        type="submit"
                        class="ml-auto flex items-center"
                        disabled={!isSubmitting}
                    >
                        Simpan Perubahan
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            class="w-5 h-5 fill-white ml-1"
                            ><path
                                d="m7 17.013 4.413-.015 9.632-9.54c.378-.378.586-.88.586-1.414s-.208-1.036-.586-1.414l-1.586-1.586c-.756-.756-2.075-.752-2.825-.003L7 12.583v4.43zM18.045 4.458l1.589 1.583-1.597 1.582-1.586-1.585 1.594-1.58zM9 13.417l6.03-5.973 1.586 1.586-6.029 5.971L9 15.006v-1.589z"
                            ></path><path
                                d="M5 21h14c1.103 0 2-.897 2-2v-8.668l-2 2V19H8.158c-.026 0-.053.01-.079.01-.033 0-.066-.009-.1-.01H5V5h6.847l2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2z"
                            ></path></svg
                        >
                    </Button>
                </div>
            </div>
        </form>
    {/key}
</div>

{#if err.status != null && err.status == true}
    <Toast color="green" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CheckCircleSolid class="w-5 h-5" />
            <span class="sr-only">Check icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
{#if err.status != null && err.status == false}
    <Toast color="red" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CloseCircleSolid class="w-5 h-5" />
            <span class="sr-only">Error icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
