<script>
    import { Heading } from "flowbite-svelte";
    import Navbar from "@C/LandingPage/nav/Navbar.svelte";
    import Footer from "@C/LandingPage/Footer/Footer.svelte";

    let admins = [
        {
            name: "Wisnu",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Wisnu.jpg",
            head: "RW 03",
            position: "",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Subianto",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Subianta.jpg",
            head: "RW 03",
            position: "RT 01",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Jaya",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Jaya.jpg",
            head: "RW 03",
            position: "RT 02",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Gungun",
            photoProfile: "https://jatim.kemenkumham.go.id/images/gungun.jpg",
            head: "RW 03",
            position: "RT 03",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Indah",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Indah.jpg",
            head: "RW 03",
            position: "RT 04",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Ufi",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Ufi.jpg",
            head: "RW 03",
            position: "RT 05",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Ratno",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Ratno.jpg",
            head: "RW 03",
            position: "RT 06",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Meirina",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Meirina.jpg",
            head: "RW 03",
            position: "RT 07",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Adi",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Adi.jpg",
            head: "RW 03",
            position: "RT 08",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
    ];
</script>

<div class="relative px-8 md:px-16 mt-24">
    <Navbar />

    <div class="content">
        <div class="text-center mb-8">
            <Heading tag="h3" class="mb-2">Ketua RW</Heading>
        </div>

        <div class="flex md:flex-row flex-col mb-16">
            <img
                src={admins[0].photoProfile}
                alt=""
                class="max-w-md md:w-4/5 object-cover rounded-lg max-h-96 object-top"
            />
            <div
                class="desc-profile p-4 md:p-8 md:flex md:justify-center md:align-center"
            >
                <div
                    class="w-4/5 md:flex md:flex-col md:justify-center md:align-center"
                >
                    <Heading tag="h4" class="mb-2">{admins[0].name}</Heading>
                    <Heading tag="h5" class="mb-2"
                        >Ketua {admins[0].head}</Heading
                    >
                    <p class="text-gray-500">{admins[0].greetings}</p>
                </div>
            </div>
        </div>

        <div
            class="list-rt grid grid-cols-2 md:grid-cols-3 gap-4 lg:gap-8 md:px-32 pb-24"
        >
            {#each admins.slice(1) as admin}
                <div class="rounded-lg shadow-lg">
                    <img
                        src={admin.photoProfile}
                        alt=""
                        class="w-full object-cover rounded-lg h-auto object-top"
                    />
                    <div class="p-4">
                        <Heading tag="h5" class="mb-2">{admin.name}</Heading>
                        <p class="text-gray-500">
                            Ketua {admin.position} / {admin.head}
                        </p>
                        <p class="text-gray-500">{admin.number}</p>
                        <p class="text-gray-500">{admin.address}</p>
                    </div>
                </div>
            {/each}
        </div>
    </div>

    <Footer />
</div>
