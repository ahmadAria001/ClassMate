<script lang="ts">
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    import { Label, Input, Checkbox, A, Button, Toast } from "flowbite-svelte";

    import axiosInstance from "axios";
    import { createForm } from "felte";
    import { validateSchema, validator } from "@felte/validator-zod";
    import { page, router } from "@inertiajs/svelte";
    import {
        type CreateSchema,
        createSchema,
    } from "@R/Utils/Schema/User/Create";

    export let site = {
        name: "KawanDesa",
        img: "assets/icons/KD_logo.svg",
        link: "/",
        imgAlt: "KawanDesa Logo",
    };

    const axios = axiosInstance.create({ withCredentials: true });
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    const { form, isSubmitting, errors } = createForm<CreateSchema>({
        extend: validator<CreateSchema>({
            schema: createSchema,
        }),
        onSubmit: async (values) => {
            const response = await axios.post("/api/user", {
                nik: values.nik,
                password: values.password,
                _token: $page.props.csrf_token,
            });
        },
        onError: (values: unknown) => {
            console.error(values?.response);
            err = values?.response?.data;

            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            return;
        },
        onSuccess: (response) => {
            router.visit("/login");
        },
    });
</script>

<main class="w-full">
    <div
        class="flex flex-col items-center justify-center px-6 pt-8 max-auto md:h-screen pt:mt-0"
    >
        <div class="md:w-1/3">
            <a
                href={site.link}
                class="flex flex-col items-center justify-center mb-3"
            >
                <img src={site.img} alt={site.imgAlt} class="mb-3" />
                <span class="text-2xl font-bold">{site.name}</span>
            </a>
            <form method="POST" use:form>
                <Label class="space-y-2 mb-3">
                    <span>NIK</span>
                    <Input
                        type="text"
                        name="nik"
                        placeholder="Masukan NIK anda"
                        required
                    />
                    {#if $errors.nik}
                        <span class="text-sm text-red-500">{$errors.nik}</span>
                    {/if}
                </Label>
                <Label class="space-y-2 mb-3">
                    <span>Password</span>
                    <Input
                        type="password"
                        name="password"
                        placeholder="Masukan password anda"
                        required
                    />
                    {#if $errors.password}
                        <span class="text-sm text-red-500"
                            >{$errors.password}</span
                        >
                    {/if}
                </Label>
                <Label class="space-y-2 mb-3">
                    <span>Konfirmasi Password</span>
                    <Input
                        type="password"
                        name="confirm_pass"
                        placeholder="Konfirmasi password anda"
                        required
                    />
                    {#if $errors.confirm_pass}
                        <span class="text-sm text-red-500"
                            >{$errors.confirm_pass}</span
                        >
                    {/if}
                </Label>
                <div class="block w-full mb-3">
                    <div class="flex items-start w-full">
                        <Checkbox color="blue" name="tos"
                            >Saya menerima syarat dan ketentuan yang berlaku</Checkbox
                        >
                    </div>
                    {#if $errors.tos}
                        <span class="text-sm text-red-500">{$errors.tos}</span>
                    {/if}
                </div>

                <Button type="submit" class="w-full" color="blue">Daftar</Button
                >

                <!-- Sementara link kearahkan ke login karena belum bisa untuk logika backend -->
                <Label class="font-bold mt-3"
                    >Sudah Punya Akun? <A href="/">Login</A></Label
                >
            </form>
        </div>
    </div>
</main>

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
