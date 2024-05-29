<script lang="ts">
    import { createForm } from "felte";
    import { validator } from "@felte/validator-zod";
    import { object, z } from "zod";
    import axios from "axios";
    import { page, router } from "@inertiajs/svelte";
    import { Login } from "@R/schema";

    import { Label, Input, Checkbox, A, Button } from "flowbite-svelte";
    export let site = {
        name: "KawanDesa",
        img: "assets/icons/KD_logo.svg",
        link: "/",
        imgAlt: "KawanDesa Logo",
    };

    let title = "Login Page";

    let errorContainer = {
        message: null,
    };

    const signIn = async (values: z.infer<typeof Login>) => {
        const response = await axios.post("/api/auth/signin", {
            username: values.username,
            password: values.password,
            _token: $page.props.csrf_token,
        });

        const { status, token, exp } = response.data;
        // if (status) setCookie("token", response.data.token, exp);

        // before '/civilian'
        router.visit("/beranda");
    };

    const errHandler = () => {};

    const { form, isSubmitting, errors } = createForm<z.infer<typeof Login>>({
        extend: validator<z.infer<typeof Login>>({ schema: Login }),
        onSubmit: signIn,
        onError: (values: unknown) => {
            if (values?.response?.status === 422) {
                $errors.username = values?.response?.data?.errors?.username;
                $errors.password = values?.response?.data?.errors?.password;
            }

            errorContainer.message = values?.response?.data?.err;
            return;
        },
        onSuccess: () => {
            console.log("Success");
        },
    });
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>
<!-- NOTE:
    - untuk type input NIK harusnya nanti number => sementara text karena dev username pakai text
    - untuk name input NIK harunysa nik bukan username
-->
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
            <form use:form method="POST">
                <Label class="space-y-2 mb-3">
                    <span class="dark:text-black">NIK</span>
                    <Input
                        class="dark:bg-white dark:text-black"
                        type="text"
                        name="username"
                        placeholder="Masukan NIK anda"
                        required
                    />
                </Label>
                {#if $errors.username}
                    <span class="text-sm text-red-500">{$errors.username}</span>
                {/if}
                <Label class="space-y-2 mb-3">
                    <span class="dark:text-black">Password</span>
                    <Input
                        class="dark:bg-white dark:text-black"
                        type="password"
                        name="password"
                        placeholder="Masukan password anda"
                        required
                    />
                </Label>
                {#if $errors.password}
                    <span class="text-sm text-red-500">{$errors.password}</span>
                {/if}
                {#if errorContainer.message}
                    <span class="text-sm text-red-500"
                        >{errorContainer.message}</span
                    >
                {/if}
                <div class="flex items-start mb-3">
                    <Checkbox color="blue" class="dark:text-black"
                        >Ingat saya</Checkbox
                    >
                    <a
                        href="/"
                        class="ml-auto text-sm font-bold text-blue-700 hover:underline dark:text-blue-500"
                        >Lupa Password</a
                    >
                </div>
                <input
                    type="submit"
                    value="Masuk"
                    disabled={$isSubmitting}
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                />
                <Label class="font-bold mt-3 dark:text-black"
                    >Belum Punya Akun? <A href="/register">Buat Akun</A></Label
                >
            </form>
        </div>
    </div>
</main>
